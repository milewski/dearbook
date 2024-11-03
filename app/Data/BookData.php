<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class BookData extends Data
{
    public function __construct(
        public string $title,
        public string $subject,
        public array $tags,
        public array $paragraphs,
    )
    {

    }

    public function isValid(): bool
    {
        return count($this->paragraphs) === 10;
    }

    public function toSummary(): string
    {
        return collect([
//            $this->title,
//            $this->subject,
//            ...$this->tags,
            ...$this->paragraphs,
        ])->implode(PHP_EOL);
    }
}
