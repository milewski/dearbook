<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookAdvancedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [ 'max:255' ],
            'prompt' => [ 'required', 'max:500', 'min:10' ],
            'negative' => [ 'max:500' ],
            'wallet' => [ 'required', 'string' ],
        ];
    }
}
