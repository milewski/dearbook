<?php

namespace App\Enums;

enum Embedding: string
{
    case MXBAI_EMBED_LARGE = 'mxbai-embed-large';
    case NOMIC_EMBED_TEXT = 'nomic-embed-text';
}
