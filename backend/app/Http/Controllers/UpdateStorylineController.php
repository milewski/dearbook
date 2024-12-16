<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStorylineRequest;
use App\Services\BookService;

class UpdateStorylineController extends Controller
{
    public function __invoke(UpdateStorylineRequest $request): bool
    {
        if ($request->header('x-api-key') !== config('app.worker_api_key')) {
            return false;
        }

        return BookService::resolve()->updateStoryline($request);
    }
}
