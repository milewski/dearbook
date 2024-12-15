<?php

declare(strict_types = 1);

namespace App\Events;

use App\Models\Book;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookWasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Book $book,
    )
    {
    }
}
