<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\ComfyUI\ComfyUIService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class GenerateAssets implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private readonly Book $book,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function handle(ComfyUIService $comfy): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $comfy->execute('main.workflow.json', $this->book);
    }
}
