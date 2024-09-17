<?php

declare(strict_types=1);

namespace App\Game\Application\Query\MapQuery;

class Field
{
    private function __construct(
        public MapCoordinates $coordinates,
        public ?Monk $monk,
        public string $color,
    ) {
    }

    /**
     * @param array{
     *     coordinates: array{row: int, column: int},
     *     monk: array{id: int, team: string}|null,
     *     color: string,
     * } $field
     */
    public static function fromArray(array $field): self
    {
        return new self(
            MapCoordinates::fromArray($field['coordinates']),
            $field['monk'] ? Monk::fromArray($field['monk']) : null,
            $field['color'],
        );
    }

    public static function empty(int $id): self
    {
        return new self(
            MapCoordinates::fromId($id),
            null,
            'black',
        );
    }
}
