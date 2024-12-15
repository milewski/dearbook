<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Services\BookService;

class GetWorkController extends Controller
{
    public function __invoke(CreateBookRequest $request): array
    {
        $book = BookService::resolve()->getPendingBook();

        return [
            'id' => $book->id,
            'prompt' => $book->user_prompt,
        ];
    }
}
