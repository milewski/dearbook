<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class FailWorkController extends Controller
{
    public function __invoke(Request $request): array
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return [];
        }

        BookService::resolve()->markBookAsFailed($request->input('id'));

        return [];
    }
}
