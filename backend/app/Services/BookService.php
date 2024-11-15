<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\BookData;
use App\Models\Book;
use App\Services\Traits\Resolvable;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
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
     * @return Collection<int, Book
     */
    public function getFailedBooks(): Collection
    {
        return Book::query()
            ->whereNull('assets')
            ->where('updated_at', '<=', now()->subMinutes(10))
            ->get();
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

    public function searchByTerm(?string $term = null): Paginator
    {
        return Book::query()
            ->whereNotNull('assets')
            ->when($term, fn (Builder $query, string $term) => $query
                ->selectRaw('id, title, assets, batch_id, embedding <=> ai.ollama_embed(?, ?) as distance', [ config('app.ollama.embedding'), $term ])
                ->orderBy('distance'),
            )
            ->when(blank($term), fn (Builder $query) => $query->inRandomOrder())
            ->simplePaginate(12);
    }

    public function findTop10SimilarEmbeddings(): Collection
    {
        $topEmbeddings = Book::query()
            ->selectRaw('embedding, COUNT(*) as frequency')
            ->groupBy('embedding')
            ->orderByDesc('frequency')
            ->limit(10);

        return Book::query()
            ->select('id', 'title')
            ->joinSub($topEmbeddings, 'top_embeddings', 'books.embedding', '=', 'top_embeddings.embedding')
            ->orderByDesc('top_embeddings.frequency')
            ->get();
    }

    public function findManyByBatchIds(array $batchIds): Collection
    {
        return Book::query()
            ->whereIn('batch_id', $batchIds)
            ->oldest()
            ->get();
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    public function createBook(string $batchId, ?string $userPrompt = null): Book
    {
        if (filled($userPrompt) && $this->isSafeForChildren($userPrompt) === false) {
            throw new Exception('the prompt is not safe for children...');
        }

        return $this->createBookModel(
            batchId: $batchId,
            userPrompt: $userPrompt,
            data: $book = $this->generateBookMainStoryLine($userPrompt),
            illustrations: $this->generateIllustrationDirectionFromParagraphs($book->paragraphs),
        );
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function generateIllustrationDirectionFromParagraphs(array $paragraphs): array
    {
        return retry($this->tries, function () use ($paragraphs) {

            $response = OllamaService::resolve()->generateJson(
                prompt: $this->describeIllustrationPrompt($paragraphs),
            );

            if (count($illustrations = $response->get('illustrations')) !== count($paragraphs)) {
                throw new Exception('Generated illustration prompt is not valid...');
            }

            foreach ($illustrations as $illustration) {

                if (!is_string($illustration)) {
                    throw new Exception('Generated illustration prompt is not valid...');
                }

            }

            return $illustrations;

        });
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ConnectionException
     */
    private function generateBookMainStoryLine(?string $prompt): BookData
    {
        return retry($this->tries, function () use ($prompt) {

            $exceptAboutTheseTopic = $this
                ->findTop10SimilarEmbeddings()
                ->map(fn (Book $book) => $book->title)
                ->implode(', ');

            $response = $this->ollama->generateJson(
                prompt: filled($prompt)
                    ? $this->generateStoryFromPrompt($prompt, $exceptAboutTheseTopic)
                    : $this->generateStoryPrompt($exceptAboutTheseTopic),
            );

            $data = BookData::from($response);

            if ($data->isValid() === false) {
                throw new Exception('generated data is not valid...');
            }

            return $data;

        });
    }

    /**
     * @throws ConnectionException
     */
    private function createBookModel(string $batchId, ?string $userPrompt, BookData $data, array $illustrations): Book
    {
        [ $embedding ] = $this->ollama->generateEmbedding([ $data->toSummary() ]);

        $book = new Book();

        $book->batch_id = $batchId;
        $book->user_prompt = $userPrompt;
        $book->subject = $data->subject;
        $book->title = $data->title;
        $book->tags = $data->tags;
        $book->embedding = $embedding;
        $book->paragraphs = $data->paragraphs;
        $book->illustrations = $illustrations;

        $book->save();

        return $book;
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws ConnectionException
     */
    private function isSafeForChildren(string $prompt): bool|int
    {
        return retry($this->tries, function () use ($prompt) {

            $response = $this->ollama->generateJson(
                prompt: $this->askIfPromptIsSafeForChildren($prompt),
            );

            if ($response->has('isSafe') === false) {
                throw new Exception('invalid json payload received...');
            }

            return $response[ 'isSafe' ] === true;

        });
    }

    private function generateStoryFromPrompt(string $prompt, string $except): string
    {
        return <<<PROMPT
        Write a humorous and engaging children's book with exactly 10 paragraphs, each paragraph should be written in a simple and playful tone suitable for children,
        and needs to be very short in length, have it written with language that’s easy to understand and captivating for young children.

        Use the following user-provided input as the theme or storyline, but ensure you follow the specified rules:

        ----- start_of_user_input_content
        $prompt
        ----- end_of_user_input_content

        Ensure that the story is original in both style and content, distinct from the themes, names, or styles used in the following titles:

        --- start existing stories
        $except
        --- end existing stories

        Each story should be unique in its plot, tone, and characters.

        Please respond in JSON format with the following structure:

        ```json
        {
            "title": "<book title>",
            "subject": "<main subject of the story and its characteristics>",
            "tags": [ "<main objects / animals / fruits present on the story>", ],
            "paragraphs": [
                "<short paragraph 1>",
                "<short paragraph 2>",
                ...
                "<short paragraph 10>"
            ]
        }
        ```

        Make sure the JSON response is valid and correctly structured.
        PROMPT;
    }

    private function generateStoryPrompt(string $except): string
    {
        return <<<PROMPT
        Write a humorous and engaging children's book with exactly 10 paragraphs, each paragraph should be written in a simple and playful tone suitable for children,
        and needs to be very short in length, have it written with language that’s easy to understand and captivating for young children.

        Ensure that the story is original in both style and content, distinct from the themes, names, or styles used in the following titles:

        --- start existing stories
        $except
        --- end existing stories

        Each story should be unique in its plot, tone, and characters.

        Please respond in JSON format with the following structure:

        ```json
        {
            "title": "<book title>",
            "subject": "<main subject of the story and its characteristics>",
            "tags": [ "<main objects / animals / fruits present on the story>", ],
            "paragraphs": [
                "<short paragraph 1>",
                "<short paragraph 2>",
                ...
                "<short paragraph 10>"
            ]
        }
        ```

        Make sure the JSON response is valid and correctly structured.
        PROMPT;
    }

    private function describeIllustrationPrompt(array $paragraphs): string
    {
        $paragraphs = collect($paragraphs)
            ->map(fn (string $paragraph, int $index) => sprintf('%d: %s', ++$index, $paragraph))
            ->implode(PHP_EOL);

        return <<<PROMPT
        Generate a creative prompt for a generative image AI tool to create an illustration for each paragraph in the following children's book. For each illustration prompt:

        1. **Describe the main action** occurring in the paragraph.
        2. **Include all relevant contextual elements** in each prompt individually, as the AI will not have context from previous paragraphs. Ensure each prompt fully describes the mood, setting, and character details.
        3. **Maintain consistency** with the overall tone and visual style of the book.
        4. **Refer to characters by their type,** not by their names. For example:
           - If the paragraph mentions "Sammy," refer to him as "a turtle" instead of using his name.
           - If the paragraph mentions "Willy," refer to him as "a boy" rather than by his name.

        The final response should be in the following JSON format, where each paragraph's prompt is a string within the "illustrations" array:

        ```json
        {
            "illustrations": [
                "<prompt for paragraph 1>",
                "<prompt for paragraph 2>",
                ...
                "<prompt for paragraph 10>",
            ]
        }

        $paragraphs
        PROMPT;
    }

    private function askIfPromptIsSafeForChildren(string $prompt): string
    {
        return <<<PROMPT
        Analyze the following user input prompt and determine if it is an appropriate and safe theme for a children's book.
        Consider themes, language, and any sensitive content to ensure suitability for young readers.

        ----- start_of_user_input_content
        $prompt
        ----- end_of_user_input_content

        Return only the following JSON response:

        ```json
        {
          "isSafe": <boolean>
        }
        ```

        Make sure the JSON response is valid and correctly structured.
        PROMPT;
    }
}
