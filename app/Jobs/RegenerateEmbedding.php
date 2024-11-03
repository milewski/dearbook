<?php

namespace App\Jobs;

use App\Data\BookData;
use App\Enums\Embedding;
use App\Models\Book;
use App\Services\OllamaService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;

class RegenerateEmbedding implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private readonly Book $book,
        private readonly Embedding $embedding,
    )
    {
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
                embedding: $this->embedding,
                inputs: [ BookData::from($this->book)->toSummary() ],
            )
            ->first();

        $this->book->save();
    }
}
