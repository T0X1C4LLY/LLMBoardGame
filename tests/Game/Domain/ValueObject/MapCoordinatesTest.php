<?php

declare(strict_types=1);

namespace App\Tests\Game\Domain\ValueObject;

use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\Exception\InvalidDirectionException;
use App\Game\Domain\ValueObject\MapCoordinates;
use PHPUnit\Framework\TestCase;

class MapCoordinatesTest extends TestCase
{
    /**
     * @dataProvider invalidDataProvider
     */
    public function testConstruct(int $row, int $column): void
    {
        $this->expectException(InvalidCoordinatesException::class);

        new MapCoordinates($row, $column);
    }

    public static function invalidDataProvider(): array
    {
        return [
            [
                -1,
                1
            ],
            [
                10,
                0
            ],
            [
                3,
                -1
            ],
            [
                4,
                16
            ],
            [
                1,
                15
            ]
        ];
    }

    /**
     * @dataProvider coordinatesProvider
     */
    public function testGetNeighbours(MapCoordinates $mapCoordinates, array $expectedNeighbours): void
    {
        $neighbours = $mapCoordinates->getNeighbours();

        self::assertCount(count($expectedNeighbours), $neighbours);

        foreach ($neighbours as $neighbour) {
            $arrayOfCompare = [];

            foreach ($expectedNeighbours as $expectedNeighbour) {
                $arrayOfCompare[] = $neighbour->same($expectedNeighbour);
            }

            self::assertContains(true, $arrayOfCompare);
            self::assertCount(count($expectedNeighbours), $arrayOfCompare);
        }
    }

    public static function coordinatesProvider(): array
    {
        return [
            [
                new MapCoordinates(0, 0),
                [
                    new MapCoordinates(0, 1),
                    new MapCoordinates(1, 0),
                ],
            ],
            [
                new MapCoordinates(0, 1),
                [
                    new MapCoordinates(0, 0),
                    new MapCoordinates(0, 2),
                    new MapCoordinates(1, 0),
                    new MapCoordinates(1, 1),
                ],
            ],
            [
                new MapCoordinates(0, 15),
                [
                    new MapCoordinates(0, 14),
                    new MapCoordinates(1, 14),
                ],
            ],
            [
                new MapCoordinates(1, 0),
                [
                    new MapCoordinates(0, 0),
                    new MapCoordinates(0, 1),
                    new MapCoordinates(1, 1),
                    new MapCoordinates(2, 0),
                    new MapCoordinates(2, 1),
                ],
            ],
            [
                new MapCoordinates(1, 5),
                [
                    new MapCoordinates(0, 5),
                    new MapCoordinates(0, 6),
                    new MapCoordinates(1, 4),
                    new MapCoordinates(1, 6),
                    new MapCoordinates(2, 5),
                    new MapCoordinates(2, 6),
                ],
            ],
            [
                new MapCoordinates(1, 14),
                [
                    new MapCoordinates(0, 14),
                    new MapCoordinates(0, 15),
                    new MapCoordinates(1, 13),
                    new MapCoordinates(2, 14),
                    new MapCoordinates(2, 15),
                ],
            ],
            [
                new MapCoordinates(2, 0),
                [
                    new MapCoordinates(1, 0),
                    new MapCoordinates(2, 1),
                    new MapCoordinates(3, 0),
                ],
            ],
            [
                new MapCoordinates(2, 4),
                [
                    new MapCoordinates(1, 3),
                    new MapCoordinates(1, 4),
                    new MapCoordinates(2, 3),
                    new MapCoordinates(2, 5),
                    new MapCoordinates(3, 3),
                    new MapCoordinates(3, 4),
                ],
            ],
            [
                new MapCoordinates(2, 15),
                [
                    new MapCoordinates(1, 14),
                    new MapCoordinates(2, 14),
                    new MapCoordinates(3, 14),
                ],
            ],
            [
                new MapCoordinates(9, 0),
                [
                    new MapCoordinates(8, 0),
                    new MapCoordinates(8, 1),
                    new MapCoordinates(9, 1),
                ],
            ],
            [
                new MapCoordinates(9, 2),
                [
                    new MapCoordinates(8, 2),
                    new MapCoordinates(8, 3),
                    new MapCoordinates(9, 1),
                    new MapCoordinates(9, 3),
                ],
            ],
            [
                new MapCoordinates(9, 14),
                [
                    new MapCoordinates(8, 14),
                    new MapCoordinates(8, 15),
                    new MapCoordinates(9, 13),
                ],
            ],
            [
                new MapCoordinates(0, 14),
                [
                    new MapCoordinates(0, 13),
                    new MapCoordinates(0, 15),
                    new MapCoordinates(1, 13),
                    new MapCoordinates(1, 14),
                ],
            ],
        ];
    }

    /**
     * @dataProvider coordinatesAndDirectionsProvider
     */
    public function testGetNeighbourDirection(
        MapCoordinates $mapCoordinates,
        MapCoordinates $neighbourCoordinates,
        Direction $expectedDirection,
    ): void {
        self::assertSame($mapCoordinates->getNeighbourDirection($neighbourCoordinates), $expectedDirection);
    }

    /**
     * @dataProvider coordinatesAndDirectionsProvider
     */
    public function testFindCoordinatesForMove(
        MapCoordinates $mapCoordinates,
        MapCoordinates $neighbourCoordinates,
        Direction $direction,
    ): void {
        self::assertSame($mapCoordinates->findCoordinatesForMove($direction), [
            'row' => (string) $neighbourCoordinates->row,
            'column' => (string) $neighbourCoordinates->column,
        ]);
    }

    public static function coordinatesAndDirectionsProvider(): array
    {
        return [
            [
                new MapCoordinates(0, 0),
                new MapCoordinates(0, 1),
                Direction::EAST,
            ],
            [
                new MapCoordinates(0, 1),
                new MapCoordinates(0, 0),
                Direction::WEST,
            ],
            [
                new MapCoordinates(0, 0),
                new MapCoordinates(1, 0),
                Direction::SOUTH_EAST,
            ],
            [
                new MapCoordinates(1, 0),
                new MapCoordinates(0, 0),
                Direction::NORTH_WEST,
            ],
            [
                new MapCoordinates(1, 0),
                new MapCoordinates(2, 1),
                Direction::SOUTH_EAST,
            ],
            [
                new MapCoordinates(9, 3),
                new MapCoordinates(8, 3),
                Direction::NORTH_WEST,
            ],
            [
                new MapCoordinates(6, 2),
                new MapCoordinates(7, 1),
                Direction::SOUTH_WEST,
            ],
        ];
    }

    /**
     * @dataProvider notNeighbourProvider
     */
    public function testGetNeighbourDirectionWillThrowException(MapCoordinates $mapCoordinates, MapCoordinates $notNeighbour): void
    {
        $this->expectException(InvalidDirectionException::class);

        $mapCoordinates->getNeighbourDirection($notNeighbour);
    }

    public static function notNeighbourProvider(): array
    {
        return [
            [
                new MapCoordinates(0, 1),
                new MapCoordinates(0, 3),
            ],
            [
                new MapCoordinates(0, 1),
                new MapCoordinates(1, 3),
            ],
            [
                new MapCoordinates(1, 0),
                new MapCoordinates(2, 2),
            ],
            [
                new MapCoordinates(8, 0),
                new MapCoordinates(9, 1),
            ],
        ];
    }
}
