<?php

declare(strict_types=1);

namespace App\Game\Domain\ValueObject;

use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\Exception\InvalidDirectionException;
use JsonSerializable;

readonly class MapCoordinates implements JsonSerializable
{
    public int $row;
    public int $column;

    /**
     * @throws InvalidCoordinatesException
     */
    public function __construct(
        int $row,
        int $column,
    ) {
        if ($row < 0 || $row > 9) {
            throw InvalidCoordinatesException::byCoordinates($row, $column);
        }
        if ($column < 0 || $column > 15) {
            throw InvalidCoordinatesException::byCoordinates($row, $column);
        }
        if (1 === ($row % 2) && $column > 14) {
            throw InvalidCoordinatesException::byCoordinates($row, $column);
        }

        $this->row = $row;
        $this->column = $column;
    }

    public function same(MapCoordinates $mapCoordinates): bool
    {
        return $mapCoordinates->row === $this->row && $mapCoordinates->column === $this->column;
    }

    /**
     * @throws InvalidDirectionException
     */
    public function getNeighbourDirection(MapCoordinates $mapCoordinates): Direction
    {
        if ($this->row === $mapCoordinates->row) {
            switch ($mapCoordinates->column) {
                case $this->column - 1:
                    return Direction::WEST;
                case $this->column + 1:
                    return Direction::EAST;
            }
        }

        switch ($this->row % 2) {
            case 0:
                switch ($mapCoordinates->row) {
                    case $this->row - 1:
                        switch ($mapCoordinates->column) {
                            case $this->column - 1:
                                return Direction::NORTH_WEST;
                            case $this->column:
                                return Direction::NORTH_EAST;
                        }
                        break;
                    case $this->row + 1:
                        switch ($mapCoordinates->column) {
                            case $this->column - 1:
                                return Direction::SOUTH_WEST;
                            case $this->column:
                                return Direction::SOUTH_EAST;
                        }
                        break;
                }
                break;
            case 1:
                switch ($mapCoordinates->row) {
                    case $this->row - 1:
                        switch ($mapCoordinates->column) {
                            case $this->column:
                                return Direction::NORTH_WEST;
                            case $this->column + 1:
                                return Direction::NORTH_EAST;
                        }
                        break;
                    case $this->row + 1:
                        switch ($mapCoordinates->column) {
                            case $this->column:
                                return Direction::SOUTH_WEST;
                            case $this->column + 1:
                                return Direction::SOUTH_EAST;
                        }
                        break;
                }
                break;
        }

        throw InvalidDirectionException::byMapCoordinates($this, $mapCoordinates);
    }

    /**
     * @return MapCoordinates[]
     */
    public function getNeighbours(): array
    {
        $neighbours = [];
        $arrayOfCoordinates = [
            [
                $this->row - 1,
                $this->column,
            ],
            [
                $this->row,
                $this->column - 1,
            ],
            [
                $this->row,
                $this->column + 1,
            ],
            [
                $this->row + 1,
                $this->column,
            ],
        ];

        switch ($this->row % 2) {
            case 0:
                $arrayOfCoordinates[] = [
                    $this->row - 1,
                    $this->column - 1,
                ];
                $arrayOfCoordinates[] = [
                    $this->row + 1,
                    $this->column - 1,
                ];
                break;
            case 1:
                $arrayOfCoordinates[] = [
                    $this->row - 1,
                    $this->column + 1,
                ];
                $arrayOfCoordinates[] = [
                    $this->row + 1,
                    $this->column + 1,
                ];
                break;
        }

        foreach ($arrayOfCoordinates as $coordinates) {
            try {
                $neighbours[] = new MapCoordinates(...$coordinates);
            } catch (InvalidCoordinatesException) {
            }
        }

        return $neighbours;
    }

    /**
     * @return array{row: string, column: string}
     */
    public function findCoordinatesForMove(Direction $direction): array
    {
        return match ($direction) {
            Direction::NORTH_EAST => $this->getCoordinatesForNorthEast(),
            Direction::EAST => $this->getCoordinatesForEast(),
            Direction::SOUTH_EAST => $this->getCoordinatesForSouthEast(),
            Direction::SOUTH_WEST => $this->getCoordinatesForSouthWest(),
            Direction::WEST => $this->getCoordinatesForWest(),
            Direction::NORTH_WEST => $this->getCoordinatesForNorthWest(),
        };
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForNorthEast(): array
    {
        if (0 === $this->row % 2) {
            return [
                'row' => (string) ($this->row - 1),
                'column' => (string) $this->column,
            ];
        }

        return [
            'row' => (string) ($this->row - 1),
            'column' => (string) ($this->column + 1),
        ];
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForEast(): array
    {
        return [
            'row' => (string) $this->row,
            'column' => (string) ($this->column + 1),
        ];
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForWest(): array
    {
        return [
            'row' => (string) $this->row,
            'column' => (string) ($this->column - 1),
        ];
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForSouthEast(): array
    {
        if (0 === $this->row % 2) {
            return [
                'row' => (string) ($this->row + 1),
                'column' => (string) $this->column,
            ];
        }

        return [
            'row' => (string) ($this->row + 1),
            'column' => (string) ($this->column + 1),
        ];
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForSouthWest(): array
    {
        if (0 === $this->row % 2) {
            return [
                'row' => (string) ($this->row + 1),
                'column' => (string) ($this->column - 1),
            ];
        }

        return [
            'row' => (string) ($this->row + 1),
            'column' => (string) $this->column,
        ];
    }

    /**
     * @return array{row: string, column: string}
     */
    private function getCoordinatesForNorthWest(): array
    {
        if (0 === $this->row % 2) {
            return [
                'row' => (string) ($this->row - 1),
                'column' => (string) ($this->column - 1),
            ];
        }

        return [
            'row' => (string) ($this->row - 1),
            'column' => (string) $this->column,
        ];
    }

    public function getDistanceToCoordinates(MapCoordinates $coordinates): int
    {
        return abs($this->row - $coordinates->row) + abs($this->column - $coordinates->column);
    }

    /**
     * @return array{row: int, column: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
        ];
    }
}
