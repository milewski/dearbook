<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $id
 * @property string $reason
 */
class WorkFailureRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => Rule::exists(Book::class),
            'reason' => [ 'required' ],
        ];
    }
}
