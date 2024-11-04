<?php

declare(strict_types = 1);

namespace App\Events;

use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GenerationComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private readonly Book $book,
    )
    {
    }

    public function broadcastWith(): array
    {
        return (new BookIndexResource($this->book))->toArray(request());
    }

    public function broadcastOn(): Channel
    {
        return new Channel($this->book->batch_id);
    }
}
