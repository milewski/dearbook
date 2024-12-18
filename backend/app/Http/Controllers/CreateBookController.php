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
        $user = $request->fingerprint();
        $book = RateLimiter::attempt(
            key: "create-book:$user",
            maxAttempts: 3,
            callback: fn () => tap(
                value: $service->createPendingBook($request),
                callback: fn (Book $book) => ProcessOllamaQueries::dispatch($book),
            ),
            decaySeconds: 60 * 60 * 24,
        );

        return $book
            ? [ 'id' => $book->id ]
            : [ 'limited' => true ];
    }
}
