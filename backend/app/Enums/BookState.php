<?php

declare(strict_types = 1);

namespace App\Enums;

enum BookState: string
{
    case PendingStoryLine = 'pending_storyline';
    case PendingIllustrations = 'pending_illustrations';
    case Completed = 'completed';
    case Failed = 'failed';
}
