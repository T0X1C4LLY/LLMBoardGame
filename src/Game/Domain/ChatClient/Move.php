<?php

declare(strict_types=1);

namespace App\Game\Domain\ChatClient;

use JsonSerializable;

readonly class Move implements JsonSerializable
{
    public function __construct(
        private string $id,
        public string $direction,
    ) {
    }

    public function jsonSerialize(): string
    {
        return sprintf('"%s": %s', $this->id, $this->direction);
    }
}
