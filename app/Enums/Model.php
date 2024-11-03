<?php

namespace App\Enums;

enum Model: string
{
    case LLAMA_31_8B = 'llama3.1:8b';
    case LLAMA_32_3B = 'llama3.2:3b';
    case LLAMA_3_8B = 'llama3:8b';
}
