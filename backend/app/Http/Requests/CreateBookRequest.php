<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Rules\Wallet;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prompt' => [ 'required', 'max:500', 'min:10' ],
            'wallet' => [ 'required', 'string', new Wallet() ],
        ];
    }
}
