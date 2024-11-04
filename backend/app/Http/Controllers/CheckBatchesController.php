<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Http\Request;

class CheckBatchesController extends Controller
{
    public function __invoke(Request $request, DatabaseBatchRepository $repository): array
    {
        $ids = $request->validate([
            'ids' => [ 'required', 'array' ],
            'ids.*' => [ 'required', 'string', 'uuid' ],
        ]);

        $response = collect();
        $batches = BookService::resolve()->findManyByBatchIds($ids[ 'ids' ]);

        /**
         * @var Book $book
         */
        foreach ($batches as $book) {

            $response->put($book->batch_id, true);

            if ($book->assets?->isNotEmpty()) {

                $response->put(
                    key: $book->batch_id,
                    value: $book ? new BookIndexResource($book) : true,
                );

            }

            if (in_array($book->batch_id, $ids[ 'ids' ]) === false) {
                $response->put($book->batch_id, false);
            }

        }

        return $response->toArray();
    }
}
