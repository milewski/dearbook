<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\AssetsWereGenerated;
use App\Events\BookWasCreated;
use App\Services\ComfyUI\ComfyUIService;
use App\Services\OllamaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class GenerateAssets implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        if (config('app.low_vram_mode') === false) {
            $this->onQueue('comfyui');
        }
    }

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(BookWasCreated $event): void
    {
        if (config('app.low_vram_mode') === true) {
            OllamaService::resolve()->unloadAll();
        }

        ComfyUIService::resolve()->execute(
            'main.workflow.json', $event->book,
        );

        AssetsWereGenerated::dispatch($event->book);
    }
}
