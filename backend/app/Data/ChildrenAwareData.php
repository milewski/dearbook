<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class ChildrenAwareData extends Data
{
    public function __construct(
        public bool $isSafe,
        public string $reason,
    )
    {

    }
}
