<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\WorkFailureRequest;
use App\Services\BookService;

class FailWorkController extends Controller
{
    public function __invoke(WorkFailureRequest $request): array
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return [];
        }

        BookService::resolve()->markBookAsFailed($request);

        return [];
    }
}
