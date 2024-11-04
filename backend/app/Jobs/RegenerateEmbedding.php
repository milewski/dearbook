<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Data\BookData;
use App\Models\Book;
use App\Services\OllamaService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;

class RegenerateEmbedding implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public function __construct(
        private readonly Book $book,
    )
    {
        if (config('app.low_vram_mode') === false) {
            $this->onQueue('ollama');
        }
    }

    /**
     * @throws ConnectionException
     */
    public function handle(OllamaService $ollama): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $this->book->embedding = $ollama
            ->generateEmbedding(
                inputs: [ BookData::from($this->book)->toSummary() ],
            )
            ->first();

        $this->book->save();
    }
}
