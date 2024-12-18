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
class BookIndexResource extends JsonResource
{
    use DynamicStorage;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => $this->imageUrl($this->assets->get('cover')),
        ];
    }
}
