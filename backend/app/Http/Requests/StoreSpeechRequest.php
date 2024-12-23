<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpeechRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assets' => [ 'required', 'array' ],
            'assets.*' => [ 'mimes:audio/mpeg,mpga,mp3,wav' ],
        ];
    }
}
