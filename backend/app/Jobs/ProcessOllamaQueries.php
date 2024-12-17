<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Models\Book;
use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessOllamaQueries implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Book $book,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        try {

            BookService::resolve()->updateStoryline(
                book: $this->book,
                storyline: BookService::resolve()->generateStoryline($this->book),
            );

        } catch (Throwable $error) {

            BookService::resolve()->markBookAsFailed($this->book, $error->getMessage());

        }
    }
}
