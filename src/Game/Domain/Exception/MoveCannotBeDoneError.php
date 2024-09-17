<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use Exception;

class MoveCannotBeDoneError extends Exception
{
    public static function byMonkIdAndDirection(int $monkId, string $direction): self
    {
        return new self(sprintf('Monk with id: %d cannot move %s', $monkId, $direction));
    }
}
