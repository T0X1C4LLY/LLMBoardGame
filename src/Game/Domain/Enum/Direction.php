<?php

declare(strict_types=1);

namespace App\Game\Domain\Enum;

use JsonSerializable;

enum Direction: string implements JsonSerializable
{
    case NORTH_EAST = 'NE';
    case EAST = 'E';
    case SOUTH_EAST = 'SE';
    case SOUTH_WEST = 'SW';
    case WEST = 'W';
    case NORTH_WEST = 'NW';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
