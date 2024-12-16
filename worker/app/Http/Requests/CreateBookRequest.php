<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prompt' => 'max:500',
        ];
    }
}
