<?php

namespace App\Http\Controllers;

use App\Enums\BookState;
use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use App\Services\BookService;
use Attestto\SolanaPhpSdk\PublicKey;
use Closure;
use Illuminate\Http\Request;
use Throwable;

class MyBooksController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'wallet' => [
                'required', function (string $attribute, mixed $value, Closure $fail) {

                    if (is_string($value) && filled($value)) {

                        try {

                            $toPublicKey = new PublicKey($value);

                            if (PublicKey::isOnCurve($toPublicKey) === false) {
                                $fail("invalid wallet.");
                            }

                        } catch (Throwable) {

                            $fail("invalid wallet.");

                        }

                    } else {

                        $fail("invalid wallet.");

                    }

                },
            ],
        ]);

        return BookService::resolve()->allByWallet($data[ 'wallet' ])->mapWithKeys(fn(Book $book) => [
            $book->id => match ($book->state) {
                BookState::Completed => new BookIndexResource($book),
                BookState::PendingStoryLine,
                BookState::PendingIllustrations => true,
                BookState::Failed => $book->reason
            },
        ]);
    }
}
