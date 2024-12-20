<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class GetAssetsWorkController extends Controller
{
    public function __invoke(Request $request): array
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return [];
        }

        if ($book = BookService::resolve()->getPendingBook()) {

            return [
                'id' => $book->id,
                'title' => $book->title,
                'synopsis' => $book->synopsis,
                'illustrations' => $book->illustrations,
                'generation_type' => $book->generation_type->value,
                'generation_data' => $book->generationData()->toArray(),
            ];

        }

        return [];
    }
}
