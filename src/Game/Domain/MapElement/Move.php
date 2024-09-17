<?php

declare(strict_types=1);

namespace App\Game\Domain\MapElement;

use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\ValueObject\MapCoordinates;
use JsonSerializable;

readonly class Move implements JsonSerializable
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
     *      id: int,
     *      team: string,
     *      moveFrom: array{
     *           row: string,
     *           column: string,
     *      },
     *      moveTo: array{
     *          row: string,
     *          column: string,
     *      },
     *      direction: string,
     *  } $move
     *
     * @throws InvalidCoordinatesException
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

    /**
     * @return array{
     *     id: int,
     *     team: string,
     *     moveFrom: array{
     *         row: int,
     *         column: int,
     *     },
     *     moveTo: array{
     *         row: int,
     *         column: int,
     *     },
     *     direction: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'team' => $this->team->jsonSerialize(),
            'moveFrom' => $this->moveFrom->jsonSerialize(),
            'moveTo' => $this->moveTo->jsonSerialize(),
            'direction' => $this->direction->value,
        ];
    }
}
