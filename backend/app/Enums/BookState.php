<?php

declare(strict_types = 1);

namespace App\Enums;

enum BookState: string
{
    case PendingStoryLine = 'pending_storyline';
    case PendingIllustrations = 'pending_illustrations';
    case PendingSpeech = 'pending_speech';
    case Completed = 'completed';
    case Failed = 'failed';
}
