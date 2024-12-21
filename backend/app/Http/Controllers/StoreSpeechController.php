<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpeechRequest;
use App\Models\Book;
use App\Services\BookService;

class StoreSpeechController extends Controller
{
    public function __invoke(Book $book, StoreSpeechRequest $request): bool
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return false;
        }

        return BookService::resolve()->storeSpeech($book, $request);
    }
}
