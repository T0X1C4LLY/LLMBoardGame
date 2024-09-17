<?php

declare(strict_types=1);

namespace App\Game\Domain\MapElement;

use App\Game\Domain\Enum\Team;
use JsonSerializable;

readonly class Monk implements JsonSerializable
{
    public function __construct(
        public int $id,
        public Team $team,
    ) {
    }

    /**
     * @return array{id: int, team: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'team' => $this->team->value,
        ];
    }
}
