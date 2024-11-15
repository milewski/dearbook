<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class GenerateBookStory implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $id,
        private readonly ?string $prompt = null,
    )
    {
        if (config('app.low_vram_mode') === false) {
            $this->onQueue('ollama');
        }
    }

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(): void
    {
        $book = BookService::resolve()->createBook(
            batchId: $this->id,
            userPrompt: $this->prompt,
        );

        GenerateBookAssets::dispatch($book);
    }
}
