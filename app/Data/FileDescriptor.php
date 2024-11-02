<?php

namespace App\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class FileDescriptor extends Data
{

    public function __construct(
        public string $filename,
        public string $subfolder,
        public string $type,
    )
    {

    }

    public function name(): string
    {
        return Str::of($this->filename)->before('_')->value();
    }
}
