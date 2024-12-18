<?php

namespace App\Http\Requests;

use Attestto\SolanaPhpSdk\PublicKey;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class DeleteBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
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
        ];
    }
}
