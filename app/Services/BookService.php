<?php

namespace App\Services;

use App\Data\BookData;
use App\Enums\Embedding;
use App\Enums\Model;
use App\Models\Book;
use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use phpDocumentor\Reflection\Exception;

class BookService
{
    use Resolvable;

    public function __construct(
        private readonly OllamaService $ollama,
    )
    {
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function createBook(): Book
    {
        $response = $this->ollama->generateJson(
            model: Model::LLAMA_31_8B,
            prompt: $this->generateStoryPrompt(),
        );

        $data = BookData::from($response);

        if ($data->isValid() === false) {
            throw new Exception('generated data is not valid...');
        }

        $response = OllamaService::resolve()->generateJson(
            model: Model::LLAMA_31_8B,
            prompt: $this->describeIllustrationPrompt($data->paragraphs)
        );

        if (count($illustrations = $response->get('illustrations')) !== count($data->paragraphs)) {
            throw new Exception('generated illustration prompt is not valid...');
        }

        [ $embedding ] = $this->ollama->generateEmbedding(Embedding::MXBAI_EMBED_LARGE, [ $data->toSummary() ]);

        $book = new Book();
        $book->subject = $data->subject;
        $book->title = $data->title;
        $book->tags = $data->tags;
        $book->embedding = $embedding;
        $book->paragraphs = $data->paragraphs;
        $book->illustrations = $illustrations;
        $book->save();

        return $book;

    }

    private function generateStoryPrompt(): string
    {
        return <<<PROMPT
        Write a humorous and engaging children's book with exactly 10 paragraphs, each paragraph should be written in a simple and playful tone suitable for children,
        and needs to be very short in length, have it written with language that’s easy to understand and captivating for young children.

        Don't write a story about the following topics: Benny the Bunny, Sock, Luna the Monkey, The Great Pizza Party

        Please respond in JSON format with the following structure:

        ```json
        {
            "title": "<book title>",
            "subject": "<main subject of the story and its characteristics>",
            "tags": [ "<main objects / animals / fruits present on the story>", ],
            "paragraphs": [
                "<10 short paragraph>"
            ]
        }
        ```

        Make sure the JSON response is valid and correctly structured.
        PROMPT;
    }

    private function describeIllustrationPrompt(array $paragraphs): string
    {
        $paragraphs = collect($paragraphs)
            ->map(fn(string $paragraph, int $index) => sprintf('%d: %s', ++$index, $paragraph))
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
            ]
        }

        $paragraphs
        PROMPT;
    }
}
