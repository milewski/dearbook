<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prompt' => [ 'required', 'max:500', 'min:10' ],
        ];
    }
}
