<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'wallet' => [ 'required', 'string' ],
        ];
    }
}
