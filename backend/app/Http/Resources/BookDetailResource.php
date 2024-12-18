<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Book
 */
class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'cover' => Storage::disk()->url($this->assets->get('cover')),
            'backdrop' => Storage::disk()->url($this->assets->get('backdrop')),
            'paragraphs' => $this->paragraphs->map(fn (string $paragraph, int $index) => [
                'illustration' => Storage::disk()->url($this->assets->get(sprintf('illustration-%d', ++$index))),
                'text' => $paragraph,
            ]),
        ];
    }
}
