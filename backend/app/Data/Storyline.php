<?php

declare(strict_types = 1);

namespace App\Data;

use PrinsFrank\Standards\Language\LanguageAlpha2;
use Spatie\LaravelData\Data;

class Storyline extends Data
{
    public function __construct(
        public LanguageAlpha2 $language,
        public StorylineData $data,
        public array $illustrations,
    )
    {
    }
}
