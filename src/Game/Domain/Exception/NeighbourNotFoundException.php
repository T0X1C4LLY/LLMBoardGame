<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use App\Game\Domain\ValueObject\MapCoordinates;
use Exception;

class NeighbourNotFoundException extends Exception
{
    public static function byCoordinates(MapCoordinates $mapCoordinates): self
    {
        return new self(sprintf(
            'Cannot get active neighbour field with data: row:%s, column:%s',
            $mapCoordinates->row,
            $mapCoordinates->column,
        ));
    }
}
