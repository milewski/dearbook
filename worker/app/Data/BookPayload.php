<?php

declare(strict_types = 1);

namespace App\Data;

use Spatie\LaravelData\Data;

class BookPayload extends Data
{
    public function __construct(
        public BookData $data,
        public array $illustrations,
    )
    {
    }
}
