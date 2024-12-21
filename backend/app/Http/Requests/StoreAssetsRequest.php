<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assets.*' => [ 'required', 'array', 'image' ],
        ];
    }
}
