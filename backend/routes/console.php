<?php

declare(strict_types = 1);

use App\Enums\BookState;
use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

Artisan::command('book:assets:s3', function () {

    config([
        'filesystems.disks.s3.read-only' => false,
    ]);

    $s3 = Storage::disk('s3');
    $public = Storage::disk('public');

    Book::query()
        ->where('state', BookState::Completed->value)
        ->chunk(100, function (Collection $books) use ($s3, $public) {

            $books->each(function (Book $book) use ($s3, $public) {

                foreach ($book->assets as $asset) {

                    if ($public->fileExists($asset)) {

                        $s3->put($asset, $public->get($asset));
                        $public->delete($asset);

                    }

                }

            });

        });

});
