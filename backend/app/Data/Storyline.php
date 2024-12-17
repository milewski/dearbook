<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class Storyline extends Data
{
    public function __construct(
        public StorylineData $data,
        public array $illustrations,
    )
    {
    }
}
