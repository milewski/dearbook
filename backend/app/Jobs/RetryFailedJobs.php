<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Events\BookWasCreated;
use App\Services\BookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RetryFailedJobs implements ShouldQueue
{
    use Queueable;

    public function handle(BookService $service): void
    {
        foreach ($service->getFailedBooks() as $book) {

            $book->touch();

            BookWasCreated::dispatch($book);

        }
    }
}
