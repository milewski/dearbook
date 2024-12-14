<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostWorkRequest;
use App\Services\BookService;

class PostWorkController extends Controller
{
    public function __invoke(PostWorkRequest $request)
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return false;
        }

        BookService::resolve()->finish($request);
    }
}
