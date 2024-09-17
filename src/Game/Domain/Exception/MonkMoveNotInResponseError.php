<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use Exception;

class MonkMoveNotInResponseError extends Exception
{
    public static function byMonkId(int $monkId): self
    {
        return new self(sprintf('Move for monk with id: %d was not in response', $monkId));
    }
}
