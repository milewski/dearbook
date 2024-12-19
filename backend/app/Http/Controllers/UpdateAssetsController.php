<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssetsRequest;
use App\Models\Book;
use App\Services\BookService;

class UpdateAssetsController extends Controller
{
    public function __invoke(Book $book, UpdateAssetsRequest $request): bool
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return false;
        }

        return BookService::resolve()->updateAssets($book, $request);
    }
}
