<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $reason
 */
class WorkFailureRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => [ 'required' ],
        ];
    }
}
