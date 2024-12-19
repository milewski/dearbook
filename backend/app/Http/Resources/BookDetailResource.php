<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Http\Resources\Traits\DynamicStorage;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Book
 */
class BookDetailResource extends JsonResource
{
    use DynamicStorage;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'cover' => $this->imageUrl($this->assets->get('cover')),
            'backdrop' => $this->imageUrl($this->assets->get('backdrop')),
            'paragraphs' => $this->paragraphs->map(fn (string $paragraph, int $index) => [
                'illustration' => $this->imageUrl($this->assets->get(sprintf('illustration-%d', ++$index))),
                'text' => $paragraph,
            ]),
        ];
    }
}
