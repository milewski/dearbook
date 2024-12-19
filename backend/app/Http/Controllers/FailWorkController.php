<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\WorkFailureRequest;
use App\Models\Book;
use App\Services\BookService;

class FailWorkController extends Controller
{
    public function __invoke(Book $book, WorkFailureRequest $request): array
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return [];
        }

        BookService::resolve()->markBookAsFailed($book, $request->reason);

        return [];
    }
}
