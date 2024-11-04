<?php

namespace App\Jobs;

use App\Services\BookService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class CreateRandomBookJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        retry(10, fn() => BookService::resolve()->createBook());
    }
}
