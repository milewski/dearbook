<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => Rule::exists(Book::class),
            'assets.*' => [ 'required', 'array', 'image' ],
        ];
    }
}
