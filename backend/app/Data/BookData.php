<?php

declare(strict_types = 1);

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
            "Title: $this->title",
            "Subject: $this->subject",
            'Tags:', ...$this->tags,
            'History:', ...$this->paragraphs,
        ])->implode(PHP_EOL);
    }
}
