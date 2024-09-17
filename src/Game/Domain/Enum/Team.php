<?php

declare(strict_types=1);

namespace App\Game\Domain\Enum;

use JsonSerializable;

enum Team: string implements JsonSerializable
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @return array{conqueringTeam: Team, conqueredTeam: Team}
     */
    public function getFightingTeams(): array
    {
        return match ($this) {
            self::RED => ['conqueringTeam' => self::GREEN, 'conqueredTeam' => self::BLUE],
            self::GREEN => ['conqueringTeam' => self::BLUE, 'conqueredTeam' => self::RED],
            self::BLUE => ['conqueringTeam' => self::RED, 'conqueredTeam' => self::GREEN],
        };
    }
}
