<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BookState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use PrinsFrank\Standards\Language\LanguageAlpha2;

/**
 * @mixin IdeHelperBook
 */
class Book extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return [
            'language' => LanguageAlpha2::class,
            'state' => BookState::class,
            'assets' => 'collection',
            'illustrations' => 'collection',
            'paragraphs' => 'collection',
            'failed' => 'boolean',
        ];
    }
}
