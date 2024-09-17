<?php

declare(strict_types=1);

namespace App\Game\Domain\Exception;

use App\Game\Domain\ChatClient\Move;
use Exception;

class InvalidChatResponseException extends Exception
{
    /**
     * @param Move[] $moves
     */
    public static function byMoves(array $moves): self
    {
        return new self(sprintf(
            'Data returned from model is invalid: %s',
            implode(',', array_map(static fn (Move $move) => $move->jsonSerialize(), $moves))
        ));
    }
}
