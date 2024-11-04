<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Jobs\CreateBookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CreateBookController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request): array
    {
        $request->validate([
            'prompt' => [ 'nullable', 'max:500' ],
        ]);

        $prompt = $request->input('prompt');
        $id = Str::uuid();

        CreateBookJob::dispatch($id, $prompt);

        return [
            'id' => $id,
        ];
    }
}
