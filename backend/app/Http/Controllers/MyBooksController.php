<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\BookState;
use App\Http\Requests\MyBookRequest;
use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Support\Collection;

class MyBooksController extends Controller
{
    public function __invoke(MyBookRequest $request): Collection
    {
        return BookService::resolve()->allByWallet($request->wallet)->mapWithKeys(fn (Book $book) => [
            $book->id => match ($book->state) {
                BookState::Completed => new BookIndexResource($book),
                BookState::PendingStoryLine,
                BookState::PendingIllustrations => true,
                BookState::Failed => $book->reason,
            },
        ]);
    }
}
