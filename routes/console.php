<?php

use App\Data\BookData;
use App\Enums\Embedding;
use App\Enums\Model;
use App\Models\Book;
use App\Services\ComfyUI\ComfyUIService;
use App\Services\OllamaService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('comfy-prompt', function () {
    ComfyUIService::resolve()->prompt();
});

Artisan::command('comfy-execute', function () {

    $book = Book::where('id', 10)->first();

    ComfyUIService::resolve()->execute('main.workflow.json', $book);

//    \App\Services\ComfyUI\ComfyUIService::resolve()->history("28763678-2039-4a92-a51e-7df5d5d21b86");
//    \App\Services\ComfyUI\ComfyUIService::resolve()->viewImage([
//        "filename" => "ComfyUI_temp_hgatp_00002_.png",
//        "subfolder" => "",
//        "type" => "temp",
//    ]);

});

Artisan::command('regenerate', function () {

    Book::all()->map(function (Book $book) {

        $book->embedding = OllamaService::resolve()
            ->generateEmbedding(
                embedding: Embedding::MXBAI_EMBED_LARGE,
                inputs: [ BookData::from($book)->toSummary() ],
            )
            ->first();

        $book->save();

        dump($book->title);

    });

});

Artisan::command('book', function () {

    function createBook(): void
    {
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

        $response = OllamaService::resolve()->generateJson(
            model: Model::LLAMA_31_8B,
            prompt: $prompt,
        );

        $data = BookData::from($response);

        if ($data->isValid() === false) {
            return;
        }

        [ $embedding ] = OllamaService::resolve()->generateEmbedding(Embedding::MXBAI_EMBED_LARGE, [ $data->toSummary() ]);

        $book = new Book();
        $book->subject = $data->subject;
        $book->title = $data->title;
        $book->tags = $data->tags;
//        $book->embedding = DB::selectOne("SELECT ai.ollama_embed('mxbai-embed-large', ?) AS embedding", [ $data->title ])->embedding;

        $book->embedding = $embedding;
        $book->paragraphs = $data->paragraphs;

        $book->save();

        ComfyUIService::resolve()->execute('main.workflow.json', $book);

    }

    foreach (range(1, 20) as $index) {
        createBook();
        echo $index . PHP_EOL;
    }

//    Book

});
