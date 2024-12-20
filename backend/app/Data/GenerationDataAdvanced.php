<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class GenerationDataAdvanced extends Data
{
    public function __construct(
        public string $title,
        public string $prompt,
        public ?string $negative,
    )
    {
    }

    public function isValid(): bool
    {
        foreach ($this->paragraphs as $paragraph) {

            if (blank($paragraph)) {
                return false;
            }

        }

        return count($this->paragraphs) === 10 && filled($this->title) && filled($this->synopsis);
    }
}
