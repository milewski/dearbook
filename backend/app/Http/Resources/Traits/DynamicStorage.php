<?php

declare(strict_types = 1);

namespace App\Http\Resources\Traits;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

trait DynamicStorage
{
    protected function imageUrl(string $path): string
    {
        foreach ([ 'public', 's3' ] as $disk) {

            if (Storage::disk($disk)->fileExists($path)) {
                return Storage::disk($disk)->url($path);
            }

        }

        throw new RuntimeException("File not found: $path");
    }
}
