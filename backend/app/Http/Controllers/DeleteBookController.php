<?php

namespace App\Http\Controllers;

use App\Enums\BookState;
use App\Http\Requests\DeleteBookRequest;
use App\Models\Book;

class DeleteBookController extends Controller
{
    public function __invoke(Book $book, DeleteBookRequest $request)
    {
        if ($book->state === BookState::Failed && $book->wallet === $request->wallet) {
            $book->delete();
        }
    }
}
