<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Storage;

class BooksController extends Controller
{
    public function __invoke(): array
    {
        $pagination = Book::whereNotNull('assets')->latest()->paginate(12);

        return [
            'data' => array_map(fn(Book $book) => (new ViewBookController)($book), $pagination->items()),
            'from' => $pagination->currentPage(),
            'next_page_url' => $pagination->nextPageUrl(),
            'previous_page_url' => $pagination->previousPageUrl(),
            'per_page' => $pagination->perPage(),
            'to' => $pagination->lastItem(),
            'total' => $pagination->total(),
        ];
    }
}
