<?php

declare(strict_types = 1);

namespace App\Enums;

enum GenerationType: string
{
    case Simple = 'simple';
    case Advanced = 'advanced';
}
