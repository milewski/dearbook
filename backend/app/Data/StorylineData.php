<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class StorylineData extends Data
{
    public function __construct(
        public string $title,
        public string $synopsis,
        public array $paragraphs,
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
