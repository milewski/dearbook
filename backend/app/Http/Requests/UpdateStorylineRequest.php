<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStorylineRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [ 'required' ],
            'synopsis' => [ 'required' ],
            'paragraphs' => [ 'required' ],
            'illustrations' => [ 'required' ],
        ];
    }
}
