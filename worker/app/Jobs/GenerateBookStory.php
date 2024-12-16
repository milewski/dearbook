<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Data\Work;
use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class GenerateBookStory implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Work $work,
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
        $book = BookService::resolve()->generateStoryline($this->work);

        GenerateBookAssets::dispatch($book);
    }
}
