<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Services\BookService;
use App\Services\ComfyUI\ComfyUIService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class FetchWorkflowOutput implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function handle(BookService $service, ComfyUIService $comfyui): void
    {
        foreach ($service->getByWorkflowId() as $book) {
            $comfyui->fetchOutputs($book);
        }
    }
}
