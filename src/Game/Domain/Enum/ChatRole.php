<?php

declare(strict_types=1);

namespace App\Game\Domain\Enum;

enum ChatRole: string
{
    case SYSTEM = 'system';
    case USER = 'user';
    case ASSISTANT = 'assistant';
}
