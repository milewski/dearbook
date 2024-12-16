<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\PostWorkRequest;
use App\Services\BookService;

class PostWorkController extends Controller
{
    public function __invoke(PostWorkRequest $request)
    {
        BookService::resolve()->finish($request);
    }
}
