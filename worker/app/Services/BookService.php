<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\ChildrenAwareData;
use App\Data\Storyline;
use App\Data\StorylineData;
use App\Data\StorylineWork;
use App\Exceptions\InvalidDataGeneratedByOllama;
use App\Exceptions\UnsafeForChildrenException;
use App\Http\Requests\PostWorkRequest;
use App\Models\Book;
use App\Services\Traits\Resolvable;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
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

    public function finish(PostWorkRequest $request): void
    {
        /**
         * @var Book $book
         */
        $book = Book::find($request->input('id'));

        $book->title = $request->input('title');
        $book->synopsis = $request->input('synopsis');
        $book->assets = collect($request->files)->map(function (UploadedFile $file) {
            return $file->store();
        });

        $book->save();
    }

    public function createPendingBook(string $prompt): Book
    {
        $book = new Book();
        $book->user_prompt = $prompt;
        $book->save();

        return $book;
    }

    public function getPendingBook(): ?Book
    {
        return Book::query()
            ->whereNull('assets')
            ->orderBy('created_at')
            ->first();
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
        return Book::query()
            ->whereNotNull('assets')
            ->inRandomOrder()
            ->simplePaginate(12);
    }

    public function findManyByBatchIds(array $ids): Collection
    {
        return Book::query()
            ->whereIn('id', $ids)
            ->oldest()
            ->get();
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    public function generateStoryline(StorylineWork $work): Storyline
    {
        $childrenAwareData = $this->isSafeForChildren($work->prompt);

        if ($childrenAwareData->isSafe === false) {
            throw new UnsafeForChildrenException($childrenAwareData->reason);
        }

        return new Storyline(
            data: $book = $this->generateBookMainStoryLine($work->prompt),
            illustrations: $this->generateIllustrationDirectionFromParagraphs($book->paragraphs),
        );
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function isSafeForChildren(string $prompt): ChildrenAwareData
    {
        return retry($this->tries, function () use ($prompt) {

            $response = $this->ollama->generateJsonSchema(
                ...$this->askIfPromptIsSafeForChildren($prompt),
            );

            return ChildrenAwareData::from($response);

        });
    }

    private function askIfPromptIsSafeForChildren(string $prompt): array
    {
        $schema = [
            'type' => 'object',
            'required' => [ 'title', 'synopsis', 'paragraphs' ],
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
        Analyze the following user input prompt and determine if it is an appropriate and safe theme for a children's book.
        Consider themes, language, and any sensitive content to ensure suitability for young readers.

        ----- start_of_user_input_content
        $prompt
        ----- end_of_user_input_content

        Respond using JSON
        PROMPT;

        return [ $prompt, $schema ];
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function generateIllustrationDirectionFromParagraphs(array $paragraphs): array
    {
        return retry($this->tries, function () use ($paragraphs) {

            $illustrations = $this->describeIllustrationPrompt($paragraphs)
                ->map(function (array $response) {
                    return OllamaService::resolve()->generateJsonSchema(...$response)->get('illustration');
                });

            if (count($illustrations) !== count($paragraphs)) {
                throw new Exception('Generated illustration prompt is not valid...');
            }

            return $illustrations->toArray();

        });
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    private function generateBookMainStoryLine(?string $prompt): StorylineData
    {
        return retry($this->tries, function () use ($prompt) {

            [ $prompt, $schema ] = $this->generateStoryFromPrompt($prompt);

            $response = $this->ollama->generateJsonSchema(
                prompt: $prompt,
                schema: $schema,
            );

            $data = StorylineData::from($response);

            if ($data->isValid() === false) {
                throw new InvalidDataGeneratedByOllama();
            }

            return $data;

        });
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
            ->map(fn (string $paragraph, int $index) => sprintf('%d: %s', ++$index, $paragraph))
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
