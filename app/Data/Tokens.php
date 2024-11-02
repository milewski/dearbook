<?php

namespace App\Data;

use Illuminate\Support\Collection;

class Tokens
{
    private function __construct(
        public Collection $tokens = new Collection(),
    )
    {
    }

    public static function make(): static
    {
        return new static();
    }

    public function add_token(string $token, string $value): self
    {
        $this->tokens->put($token, $value);

        return $this;
    }

    public function apply(string $text): string
    {
        return $this->tokens->reduce(
            callback: fn(string $text, string $value, string $token) => str_replace($token, $value, $text),
            initial: $text
        );
    }
}
