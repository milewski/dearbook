<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStorylineRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => Rule::exists(Book::class),
            'title' => [ 'required' ],
            'synopsis' => [ 'required' ],
            'paragraphs' => [ 'required' ],
            'illustrations' => [ 'required' ],
            //            'assets.*' => [ 'required', 'array', 'image' ],
        ];
    }
}