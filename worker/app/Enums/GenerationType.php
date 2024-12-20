<?php

declare(strict_types = 1);

namespace App\Enums;

enum GenerationType: string
{
    case Simple = 'simple';
    case Advanced = 'advanced';

    public function toWorkflow(): string
    {
        return sprintf('main.%s.workflow.json', $this->value);
    }
}
