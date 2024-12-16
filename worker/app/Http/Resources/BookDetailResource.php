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
class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'synopsis' => $this->resource->synopsis,
            'cover' => Storage::disk('public')->url($this->resource->assets->get('cover')),
            'backdrop' => Storage::disk('public')->url($this->resource->assets->get('backdrop')),
            'paragraphs' => $this->resource->paragraphs->map(fn (string $paragraph, int $index) => [
                'illustration' => Storage::disk('public')->url($this->resource->assets->get(sprintf('illustration-%d', ++$index))),
                'text' => $paragraph,
            ]),
        ];
    }
}
