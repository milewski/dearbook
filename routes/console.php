<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('book', function () {

    $response = Http::timeout(-1)
        ->asJson()
        ->post('http://ollama:11434/api/generate', [
            'model' => 'llama3',
            'prompt' => <<<PROMPT
            Write a humorous and engaging children's book for toddlers in 2 chapters. Each chapter should be written in a simple and playful tone suitable for toddlers,
            with language that’s easy to understand and captivating for young children. Aim for a narrative that encourages laughter and imagination,
            using familiar themes or funny characters toddlers can relate to, like animals or playful objects.

            Please respond in JSON format with the following structure:

            ```json
            {
                "title": "<book title>",
                "chapters": [
                    {
                        "number": "<number>",
                        "title": "<chapter title>",
                        "paragraphs": [ "<content in at least 500 words>", ],
                        "illustration": "<prompt for generating an illustration that represents the chapter>"
                    }
                ]
            }
            ```

            Make sure the JSON response is valid and correctly structured.
            PROMPT,
            "format" => "json",
            'stream' => false,
        ]);

    dd(json_decode($response->json('response')));

});
