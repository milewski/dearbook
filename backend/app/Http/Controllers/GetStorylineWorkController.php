<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class GetStorylineWorkController extends Controller
{
    public function __invoke(Request $request): array
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return [];
        }

        if ($book = BookService::resolve()->getPendingStorylines()) {

            return [
                'id' => $book->id,
                'prompt' => $book->user_prompt,
            ];

        }

        return [];
    }
}
