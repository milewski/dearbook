<?php

use App\Data\BookData;
use App\Enums\Embedding;
use App\Enums\Model;
use App\Jobs\CreateRandomBookJob;
use App\Jobs\GenerateAssets;
use App\Models\Book;
use App\Services\BookService;
use App\Services\ComfyUI\ComfyUIService;
use App\Services\OllamaService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use \Illuminate\Support\Facades\Bus;

Artisan::command('pipeline', function () {

    $batch = Bus::batch([]);

    Collection::times(5)->each(
        fn() => $batch->add(function () {
            BookService::resolve()->createBook();
        })
    );

    $batch->name('generate books');
    $batch->dispatch();

});

Artisan::command('comfy-execute', function () {

    OllamaService::resolve()->unloadAll();

    $book = Book::where('id', 8)->first();

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

    $book = BookService::resolve()->createBook();
    OllamaService::resolve()->unloadAll();
    ComfyUIService::resolve()->execute('main.workflow.json', $book);

});
