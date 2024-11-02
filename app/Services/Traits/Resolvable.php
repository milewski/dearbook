<?php

namespace App\Services\Traits;

trait Resolvable
{
    public static function resolve(): static
    {
        return resolve(static::class);
    }
}
