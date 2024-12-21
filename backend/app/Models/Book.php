<?php

declare(strict_types = 1);

namespace App\Models;

use App\Data\GenerationDataAdvanced;
use App\Data\GenerationDataSimple;
use App\Enums\BookState;
use App\Enums\GenerationType;
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
            'generation_type' => GenerationType::class,
            'generation_data' => 'json',
            'language' => LanguageAlpha2::class,
            'state' => BookState::class,
            'assets' => 'collection',
            'speech' => 'collection',
            'illustrations' => 'collection',
            'paragraphs' => 'collection',
            'failed' => 'boolean',
        ];
    }

    public function generationData(): GenerationDataSimple|GenerationDataAdvanced
    {
        return match ($this->generation_type) {
            GenerationType::Simple => GenerationDataSimple::from($this->generation_data),
            GenerationType::Advanced => GenerationDataAdvanced::from($this->generation_data),
        };
    }
}
