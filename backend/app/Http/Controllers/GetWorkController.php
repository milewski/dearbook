<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Services\BookService;

class GetWorkController extends Controller
{
    public function __invoke(): array|bool
    {
        if ($book = BookService::resolve()->getPendingBook()) {

            return [
                'id' => $book->id,
                'prompt' => $book->user_prompt,
            ];

        }

        return [];
    }
}
