<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use Exception;

class InvalidCoordinatesException extends Exception
{
    public static function byCoordinates(int $row, int $column): self
    {
        return new self(sprintf('Cannot creates coordinates with data: row:%s, column:%s', $row, $column));
    }
}
