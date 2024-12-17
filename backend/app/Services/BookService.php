<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\ChildrenAwareData;
use App\Data\Storyline;
use App\Data\StorylineData;
use App\Enums\BookState;
use App\Exceptions\InvalidDataGeneratedByOllama;
use App\Exceptions\UnsafeForChildrenException;
use App\Http\Requests\StoreAssetsRequest;
use App\Models\Book;
use App\Services\Traits\Resolvable;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Uid\Ulid;
use Throwable;

class BookService
{
    use Resolvable;

    private int $tries = 5;

    public function __construct(
        private readonly OllamaService $ollama,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    public function generateStoryline(Book $book): Storyline
    {
        $childrenAwareData = $this->isSafeForChildren($book->user_prompt);

        if ($childrenAwareData->isSafe === false) {
            throw new UnsafeForChildrenException($childrenAwareData->reason);
        }

        return retry(3, function () use ($book) {
            return new Storyline(
                data: $book = $this->generateBookMainStoryLine($book->user_prompt),
                illustrations: $this->generateIllustrationDirectionFromParagraphs($book->paragraphs),
            );
        });
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function isSafeForChildren(string $prompt): ChildrenAwareData
    {
        return ChildrenAwareData::from(
            $this->ollama->generateJsonSchema(
                ...$this->askIfPromptIsSafeForChildren($prompt),
            ),
        );
    }

    private function askIfPromptIsSafeForChildren(string $prompt): array
    {
        $schema = [
            'type' => 'object',
            'required' => [ 'isSafe', 'reason' ],
            'properties' => [
                'isSafe' => [
                    'type' => 'boolean',
                ],
                'reason' => [
                    'type' => 'string',
                ],
            ],
        ];

        $prompt = <<<PROMPT
        Analyze the following user input prompt and flag it as unsafe if it contains any language related to pornography, sex, or drugs.

        ----- start_of_user_input_content
        $prompt
        ----- end_of_user_input_content

        Respond using JSON
        PROMPT;

        return [ $prompt, $schema ];
    }

    public function retryUncompletedBooks(): void
    {
        Book::query()
            ->whereIn('state', [
                BookState::PendingStoryLine->value,
                BookState::PendingIllustrations->value,
            ])
            ->where('fetched_at', '<', now()->subMinutes(30))
            ->update([
                'fetched_at' => null,
            ]);
    }

    public function markBookAsFailed(Book $book, string $reason): void
    {
        $book->reason = $reason;
        $book->state = BookState::Failed;
        $book->save();
    }

    public function updateStoryline(Book $book, Storyline $storyline): bool
    {
        $book->title = $storyline->data->title;
        $book->synopsis = $storyline->data->synopsis;
        $book->paragraphs = $storyline->data->paragraphs;
        $book->illustrations = $storyline->illustrations;
        $book->state = BookState::PendingIllustrations;
        $book->fetched_at = null;

        return $book->save();
    }

    public function storeAssets(Book $book, StoreAssetsRequest $request): bool
    {
        $book->assets = collect($request->allFiles())->mapWithKeys(fn(UploadedFile $file, string $name) => [
            $name => $file->store(options: [ 'disk' => 'public' ]),
        ]);

        $book->state = BookState::Completed;
        $book->fetched_at = null;

        return $book->save();
    }

    public function createPendingBook(string $prompt): Book
    {
        $book = new Book();
        $book->user_prompt = $prompt;
        $book->save();

        return $book;
    }

    public function getPendingStorylines(): ?Book
    {
        $book = Book::query()
            ->where('state', BookState::PendingStoryLine)
            ->where('fetched_at', null)
            ->orderBy('created_at')
            ->first();

        if ($book) {
            $book->touch('fetched_at');
        }

        return $book;
    }

    public function getPendingBook(): ?Book
    {
        $book = Book::query()
            ->where('state', BookState::PendingIllustrations)
            ->where('fetched_at', null)
            ->orderByDesc('created_at')
            ->first();

        if ($book) {
            $book->touch('fetched_at');
        }

        return $book;
    }

    /**
     * @return Collection<int, Book
     */
    public function getByWorkflowId(): Collection
    {
        return Book::query()
            ->whereNotNull('workflow_id')
            ->whereNull('assets')
            ->get();
    }

    public function getRandomBooks(): Paginator
    {
        return cache()->flexible('books', [ 5, 10 ], function () {

            return Book::query()
                ->where('state', BookState::Completed)
                ->inRandomOrder()
                ->simplePaginate(12);

        });
    }

    public function findManyByBatchIds(array $ids): Collection
    {
        return Book::query()
            ->whereIn('id', $ids)
            ->where('state', BookState::Completed)
            ->oldest()
            ->limit(10)
            ->get();
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function generateIllustrationDirectionFromParagraphs(array $paragraphs): array
    {
        $illustrations = OllamaService::resolve()
            ->pool($this->describeIllustrationPrompt($paragraphs))
            ->map(fn(\Illuminate\Support\Collection $response) => $response->get('illustration'));

        if (count($illustrations) !== count($paragraphs)) {
            throw new Exception('Generated illustration prompt is not valid...');
        }

        return $illustrations->toArray();
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    private function generateBookMainStoryLine(?string $prompt): StorylineData
    {
        $data = StorylineData::from(
            $this->ollama->generateJsonSchema(
                ...$this->generateStoryFromPrompt($prompt),
            ),
        );

        if ($data->isValid() === false) {
            throw new InvalidDataGeneratedByOllama();
        }

        return $data;
    }

    private function generateStoryFromPrompt(string $prompt): array
    {
        $schema = [
            'type' => 'object',
            'required' => [ 'title', 'synopsis', 'paragraphs' ],
            'properties' => [
                'title' => [
                    'type' => 'string',
                ],
                'synopsis' => [
                    'type' => 'string',
                ],
                'paragraphs' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ];

        $prompt = <<<PROMPT
        Write a humorous and engaging children's book with exactly 10 paragraphs, each paragraph should be written in a simple and playful tone suitable for children,
        and needs to be very short in length, have it written with language that’s easy to understand and captivating for young children.

        Use the following user-provided input as the theme or storyline, but ensure you follow the specified rules:

        ----- start_of_user_input_content
        $prompt
        ----- end_of_user_input_content

        Each story should be unique in its plot, tone, and characters.

        Respond using JSON
        PROMPT;

        return [ $prompt, $schema ];
    }

    private function describeIllustrationPrompt(array $paragraphs): \Illuminate\Support\Collection
    {
        $story = collect($paragraphs)
            ->map(fn(string $paragraph, int $index) => sprintf('%d: %s', ++$index, $paragraph))
            ->implode(PHP_EOL);

        $schema = [
            'type' => 'object',
            'required' => [ 'illustration' ],
            'properties' => [
                'illustration' => [
                    'type' => 'string',
                ],
            ],
        ];

        return collect($paragraphs)->map(function (string $paragraph) use ($story, $schema) {

            $prompt = <<<PROMPT
            Generate a creative image prompt for a generative AI tool to create an illustration for the following paragraph in the children's book. You will receive the full story context for reference, but respond with one image prompt at a time, focusing on the provided paragraph.

            ----- start_of_story
            $story
            ----- end_of_story

            For the provided paragraph, follow these instructions:
            1. **Summarize the main action** occurring in the paragraph.
            2. **Describe the setting, mood, and characters** with enough detail so the AI can visualize the scene, ensuring it makes sense even when the paragraph is isolated.
            3. **Use broad character descriptions** rather than names. For example, if the paragraph mentions a character like "Sammy," refer to them as "a turtle," or "Willy" as "a boy."
            4. **Ensure consistency** with the overall visual style and tone of the full story.

            ----- start_of_paragraph
            $paragraph
            ----- end_of_paragraph

            For the paragraph provided, do the following:
            1. Identify the key scene and describe it in detail, including any relevant emotions or atmosphere.
            2. Use context from the full story to maintain accuracy and consistency in tone and style.

            Respond in JSON format for the paragraph provided.
            PROMPT;

            return [
                $prompt,
                $schema,
            ];

        });
    }
}
