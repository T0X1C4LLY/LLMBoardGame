<?php

declare(strict_types=1);

namespace App\Game\Domain\Enum;

enum FieldActivityStatus: string
{
    case ACTIVE = 'black';
    case INACTIVE = 'white';
}
