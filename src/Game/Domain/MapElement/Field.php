<?php

declare(strict_types=1);

namespace App\Game\Domain\MapElement;

use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Enum\FieldActivityStatus;
use App\Game\Domain\ValueObject\MapCoordinates;
use JsonSerializable;

readonly class Field implements JsonSerializable
{
    public function __construct(
        public MapCoordinates $coordinates,
        public ?Monk $monk,
        public string $color,
    ) {
    }

    public function isActive(): bool
    {
        return FieldActivityStatus::INACTIVE->value !== $this->color;
    }

    /**
     * @return MapCoordinates[]
     */
    public function getNeighboursCoordinates(): array
    {
        return $this->coordinates->getNeighbours();
    }

    /**
     * @return array{
     *     id: int,
     *     team: string,
     *     moveFrom: array{
     *          row: string,
     *          column: string,
     *     },
     *     moveTo: array{
     *         row: string,
     *         column: string,
     *     },
     *     direction: string,
     * }
     */
    public function getMoveInfo(Direction $direction): array
    {
        /** @var Monk $monk */
        $monk = $this->monk;

        return [
            'id' => $monk->id,
            'team' => $monk->team->value,
            'moveFrom' => [
                'row' => (string) $this->coordinates->row,
                'column' => (string) $this->coordinates->column,
            ],
            'moveTo' => $this->coordinates->findCoordinatesForMove($direction),
            'direction' => $direction->value,
        ];
    }

    /**
     * @return array{
     *     monk: array{id: int, team: string}| null,
     *     color: string,
     *     coordinates: array{row: int, column: int},
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'monk' => $this->monk?->jsonSerialize(),
            'color' => $this->color,
            'coordinates' => $this->coordinates->jsonSerialize(),
        ];
    }
}
