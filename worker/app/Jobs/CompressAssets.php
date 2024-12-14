<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class CompressAssets implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Book $book,
    )
    {
    }

    public function handle(): void
    {
        foreach ($this->book->assets as $_ => $asset) {

            ImageOptimizer::optimize(
                Storage::disk('public')->path($asset),
            );

        }
    }
}
