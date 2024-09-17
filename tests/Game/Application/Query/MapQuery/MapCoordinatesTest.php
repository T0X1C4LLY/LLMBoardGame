<?php

namespace App\Tests\Game\Application\Query\MapQuery;

use App\Game\Application\Query\MapQuery\MapCoordinates;
use PHPUnit\Framework\TestCase;

class MapCoordinatesTest extends TestCase
{
    /**
     * @dataProvider idAndCoordinatesProvider
     */
    public function testFromId(int $id, MapCoordinates $actualCoordinates): void
    {
        $coordinates = MapCoordinates::fromId($id);
        self::assertSame($coordinates->column, $actualCoordinates->column);
        self::assertSame($coordinates->row, $actualCoordinates->row);
    }

    public static function idAndCoordinatesProvider(): array
    {
        return [
            [
                0,
                MapCoordinates::fromArray(['row' => 0, 'column' => 0])
            ],
            [
                1,
                MapCoordinates::fromArray(['row' => 0, 'column' => 1])
            ],
            [
                15,
                MapCoordinates::fromArray(['row' => 0, 'column' => 15])
            ],
            [
                16,
                MapCoordinates::fromArray(['row' => 1, 'column' => 0])
            ],
            [
                30,
                MapCoordinates::fromArray(['row' => 1, 'column' => 14])
            ],
            [
                31,
                MapCoordinates::fromArray(['row' => 2, 'column' => 0])
            ],
            [
                154,
                MapCoordinates::fromArray(['row' => 9, 'column' => 14])
            ],
        ];
    }
}
