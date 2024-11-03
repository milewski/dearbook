<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Vector;

/**
 * @mixin IdeHelperBook
 */
class Book extends Model
{
    protected function casts(): array
    {
        return [
            'embedding' => Vector::class,
            'assets' => 'collection',
            'illustrations' => 'collection',
            'tags' => 'collection',
            'paragraphs' => 'collection',
        ];
    }
}
