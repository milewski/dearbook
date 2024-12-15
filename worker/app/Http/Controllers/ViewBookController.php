<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\BookDetailResource;
use App\Models\Book;

class ViewBookController extends Controller
{
    public function __invoke(Book $book): BookDetailResource
    {
        return new BookDetailResource($book);
    }
}
