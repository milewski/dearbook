<?php

namespace App\Jobs;

use App\Services\BackendService;
use App\Services\BookService;
use App\Services\ComfyUI\ComfyUIService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Throwable;

class ProcessOllamaQueries implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * @throws Throwable
     */
    public function handle(BackendService $service): void
    {
        if ($work = BackendService::resolve()->getStorylineWork()) {

            try {

                $service->updateBookStoryline(
                    id: $work->id,
                    payload: BookService::resolve()->generateStoryline($work),
                );

            } catch (Throwable $error) {

                $service->reportFailure($work->id, $error->getMessage());

            }

        }
    }
}
