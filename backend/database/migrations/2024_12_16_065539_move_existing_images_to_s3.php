<?php

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration {
    public function up(): void
    {
        Book::query()->whereNotNull('assets')->chunk(100, function (Collection $books) {

            foreach ($books as $book) {

                foreach ($book->assets as $asset) {

                    Storage::disk('s3')->put($asset, file_get_contents($asset));
                    Storage::disk('local')->delete($asset);

                }

            }

        });
    }
};
