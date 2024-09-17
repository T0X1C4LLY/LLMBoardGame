<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use App\Game\Domain\ValueObject\MapCoordinates;
use Exception;

class InvalidDirectionException extends Exception
{
    public static function byMapCoordinates(MapCoordinates $coordinates, MapCoordinates $neighbourCoordinates): self
    {
        return new self(sprintf(
            'Cannot find direction for coordinates: row:%s column:%s and row:%s column:%s',
            $coordinates->row,
            $coordinates->column,
            $neighbourCoordinates->row,
            $neighbourCoordinates->column,
        ));
    }
}
