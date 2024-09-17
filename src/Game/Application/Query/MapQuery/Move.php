<?php

declare(strict_types=1);

namespace App\Game\Application\Query\MapQuery;

use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\ValueObject\MapCoordinates;

readonly class Move
{
    private function __construct(
        public int $id,
        public Team $team,
        public MapCoordinates $moveFrom,
        public MapCoordinates $moveTo,
        public Direction $direction,
    ) {
    }

    /**
     * @param array{
     *       id: int,
     *       team: string,
     *       moveFrom: array{
     *            row: string,
     *            column: string,
     *       },
     *       moveTo: array{
     *           row: string,
     *           column: string,
     *       },
     *       direction: string,
     *   } $move
     */
    public static function fromArray(array $move): self
    {
        return new self(
            $move['id'],
            Team::from($move['team']),
            new MapCoordinates(
                (int) $move['moveFrom']['row'],
                (int) $move['moveFrom']['column'],
            ),
            new MapCoordinates(
                (int) $move['moveTo']['row'],
                (int) $move['moveTo']['column'],
            ),
            Direction::from($move['direction']),
        );
    }
}
