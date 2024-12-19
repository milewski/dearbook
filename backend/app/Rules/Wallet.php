<?php

declare(strict_types = 1);

namespace App\Rules;

use Attestto\SolanaPhpSdk\PublicKey;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use RuntimeException;
use Throwable;

class Wallet implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {

            if (PublicKey::isOnCurve(new PublicKey($value))) {
                return;
            }

            throw new RuntimeException();

        } catch (Throwable) {

            $fail('Invalid wallet.');

        }
    }
}
