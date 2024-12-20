<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookAdvancedRequest;
use App\Jobs\ProcessOllamaQueries;
use App\Services\BookService;

class CreateBookAdvancedController extends Controller
{
    public function __invoke(CreateBookAdvancedRequest $request, BookService $service): array
    {
        $book = $service->createPendingBookAdvanced($request);

        ProcessOllamaQueries::dispatch($book);

        return [ 'id' => $book->id ];
    }
}
