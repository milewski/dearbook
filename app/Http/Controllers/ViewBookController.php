<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Storage;

class ViewBookController extends Controller
{
    public function __invoke(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'subject' => $book->subject,
            'cover' => Storage::disk('public')->url($book->assets->get('cover')),
            'backdrop' => Storage::disk('public')->url($book->assets->get('backdrop')),
            'paragraphs' => $book->paragraphs->map(fn(string $paragraph, int $index) => [
                'illustration' => Storage::disk('public')->url($book->assets->get(sprintf('illustration-%d', ++$index))),
                'text' => $paragraph,
            ])
        ];
    }
}
