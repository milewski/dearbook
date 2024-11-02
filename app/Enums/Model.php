<?php

namespace App\Enums;

enum Model: string
{
    case LLAMA_31_8B = 'llama3.1:8b';
    case LLAMA_32_8B = 'llama3.2:8b';

    case LLAMA_3_8B = 'llama3:8b';
}
