<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Models\Book;
use App\Services\ComfyUI\ComfyUIService;
use App\Services\OllamaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class GenerateBookAssets implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Book $book,
    )
    {
        if (config('app.low_vram_mode') === false) {
            $this->onQueue('comfyui');
        }
    }

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(): void
    {
        if (config('app.low_vram_mode') === true) {
            OllamaService::resolve()->unloadAll();
        }

        ComfyUIService::resolve()->execute(
            'main.workflow.json', $this->book,
        );
    }
}
