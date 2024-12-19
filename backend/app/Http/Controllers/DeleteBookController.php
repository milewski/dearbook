<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\BookState;
use App\Http\Requests\DeleteBookRequest;
use App\Models\Book;

class DeleteBookController extends Controller
{
    public function __invoke(Book $book, DeleteBookRequest $request): void
    {
        if ($book->state === BookState::Failed && $book->wallet === $request->wallet) {
            $book->delete();
        }
    }
}
