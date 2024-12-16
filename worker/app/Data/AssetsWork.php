<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class AssetsWork extends Data
{
    public function __construct(
        public string $id,
        public string $title,
        public string $synopsis,
        public array $illustrations,
    )
    {
    }
}
