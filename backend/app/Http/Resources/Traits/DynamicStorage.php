<?php

declare(strict_types = 1);

namespace App\Http\Resources\Traits;

use Illuminate\Support\Facades\Storage;

trait DynamicStorage
{
    protected function imageUrl(string $path): string
    {
        $public = Storage::disk('public');
        $s3 = Storage::disk('s3');

        if ($public->fileExists($path)) {
            return $public->url($path);
        }

        return $s3->url(sprintf('books/%s/images/%s', $this->id, $path));
    }
}
