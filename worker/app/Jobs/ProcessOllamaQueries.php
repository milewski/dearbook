<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Services\BackendService;
use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessOllamaQueries implements ShouldBeUnique, ShouldQueue
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
