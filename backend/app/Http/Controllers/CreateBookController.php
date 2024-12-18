<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Jobs\ProcessOllamaQueries;
use App\Services\BookService;
use Illuminate\Support\Facades\RateLimiter;

class CreateBookController extends Controller
{
    public function __invoke(CreateBookRequest $request, BookService $service): array
    {
        $user = $request->fingerprint();
        $book = null;

        $executed = RateLimiter::attempt(
            key: "create-book:$user",
            maxAttempts: 3,
            callback: function () use (&$book, $service, $request) {

                ProcessOllamaQueries::dispatch(
                    $book = $service->createPendingBook($request),
                );

            },
            decaySeconds: 60 * 60 * 24,
        );

        if ($executed) {

            return [
                'id' => $book->id,
            ];

        }

        return [
            'limited' => true,
        ];
    }
}
