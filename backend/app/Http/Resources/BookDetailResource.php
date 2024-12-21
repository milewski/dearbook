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
        $getSpeechForPage = function (int $page) {

            return with($this->resource->speech->get(sprintf('page-%d', $page)), function (?string $value) {
                return $value ? Storage::disk('public')->url($value) : null;
            });

        };

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'synopsis' => $this->resource->synopsis,
            'synopsis_speech' => $getSpeechForPage(0),
            'cover' => Storage::disk('public')->url($this->resource->assets->get('cover')),
            'backdrop' => Storage::disk('public')->url($this->resource->assets->get('backdrop')),
            'paragraphs' => $this->resource->paragraphs->map(fn (string $paragraph, int $index) => [
                'text' => $paragraph,
                'illustration' => Storage::disk('public')->url($this->resource->assets->get(sprintf('illustration-%d', $index + 1))),
                'speech' => $getSpeechForPage($index + 1),
            ]),
        ];
    }
}
