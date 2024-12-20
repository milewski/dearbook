<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetsRequest;
use App\Models\Book;
use App\Services\BookService;

class StoreAssetsController extends Controller
{
    public function __invoke(Book $book, StoreAssetsRequest $request): bool
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return false;
        }

        return BookService::resolve()->storeAssets($book, $request);
    }
}
