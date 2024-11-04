<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\AssetsWereGenerated;
use App\Events\GenerationComplete;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class CompressImages implements ShouldQueue
{
    public function handle(AssetsWereGenerated $event): void
    {
        foreach ($event->book->assets as $_ => $asset) {

            ImageOptimizer::optimize(
                Storage::disk('public')->path($asset),
            );

        }

        GenerationComplete::dispatch($event->book);
    }
}
