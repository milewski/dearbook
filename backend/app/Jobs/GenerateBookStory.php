<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Symfony\Component\Uid\Ulid;
use Throwable;

class GenerateBookStory implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Ulid $id,
        private readonly ?string $prompt = null,
    )
    {
        $this->onQueue('ollama');
    }

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(): void
    {
        $book = BookService::resolve()->createBook(
            id: $this->id,
            userPrompt: $this->prompt,
        );

        GenerateBookAssets::dispatch($book);
    }
}
