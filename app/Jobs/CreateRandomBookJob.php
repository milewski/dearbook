<?php

namespace App\Jobs;

use App\Data\BookData;
use App\Enums\Embedding;
use App\Enums\Model;
use App\Models\Book;
use App\Services\OllamaService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use phpDocumentor\Reflection\Exception;
use Throwable;

class CreateRandomBookJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(OllamaService $ollama): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        retry(10, function () use ($ollama) {

            $prompt = <<<PROMPT
        Write a humorous and engaging children's book with at 10 paragraphs, each paragraph should be written in a simple and playful tone suitable for children,
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

            $response = $ollama->generateJson(
                model: Model::LLAMA_31_8B,
                prompt: $prompt,
            );

            $data = BookData::from($response);

            if ($data->isValid() === false) {
                throw new Exception('generated data is not valid...');
            }

            [ $embedding ] = $ollama->generateEmbedding(Embedding::MXBAI_EMBED_LARGE, [ $data->toSummary() ]);

            $book = new Book();
            $book->subject = $data->subject;
            $book->title = $data->title;
            $book->tags = $data->tags;
            $book->embedding = $embedding;
            $book->paragraphs = $data->paragraphs;
            $book->save();

            $this->batch()->add(new GenerateAssets($book));

        });

    }
}
