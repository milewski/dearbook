<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Jobs\ProcessOllamaQueries;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Support\Facades\RateLimiter;

class CreateBookController extends Controller
{
    public function __invoke(CreateBookRequest $request, BookService $service): array
    {
        $book = $service->createPendingBook($request);

        ProcessOllamaQueries::dispatch($book);

        return [ 'id' => $book->id ];
    }
}
