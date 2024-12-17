<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CheckBatchesController extends Controller
{
    public function __invoke(Request $request, DatabaseBatchRepository $repository): Collection
    {
        $ids = $request->validate([
            'ids' => [ 'required', 'array' ],
            'ids.*' => [ 'required', 'string', 'ulid' ],
        ]);

        return BookService::resolve()->findManyByBatchIds($ids[ 'ids' ])->mapWithKeys(fn (Book $book) => [
            $book->id => new BookIndexResource($book),
        ]);
    }
}
