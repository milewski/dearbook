<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property Book $resource
 */
class BookIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'cover' => Storage::disk()->url($this->resource->assets->get('cover')),
        ];
    }
}
