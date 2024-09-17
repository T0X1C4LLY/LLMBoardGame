<?php

namespace App\Tests\Game\Domain\Entity;

use App\Game\Domain\ChatClient\Move;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\Direction;
use App\Game\Domain\Enum\Team;
use App\Core\Infrastructure\Symfony\UuidV4;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

class MapTest extends TestCase
{
    private readonly ClockInterface $clock;
    private Map $map;
    private Map $mapWithDeactivatedFields;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clock = new MockClock();

        $this->map = Map::fromArray(
            UuidV4::generateNew(),
            UuidV4::generateNew(),
            [
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 0
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 0,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 0,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 1,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 1,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 1,
                    "column" => 7,
                ],
                [
                    "monk" => [
                        "id" => 1,
                        "team" => "red"
                    ],
                    "color" => "red",
                    "row" => 1,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 1,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 4,
                ],
                [
                    "monk" => [
                        "id" => 2,
                        "team" => "blue"
                    ],
                    "color" => "blue",
                    "row" => 2,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 2,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 3,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 3,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 3,
                    "column" => 12,
                ],
                [
                    "monk" => [
                        "id" => 3,
                        "team" => "red"
                    ],
                    "color" => "red",
                    "row" => 3,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 3,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 9,
                ],
                [
                    "monk" => [
                        "id" => 5,
                        "team" => "green"
                    ],
                    "color" => "green",
                    "row" => 4,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 4,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "grey",
                    "row" => 4,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 8,
                ],
                [
                    "monk" => [
                        "id" => 4,
                        "team" => "green"
                    ],
                    "color" => "green",
                    "row" => 5,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 14,
                ]
            ],
            $this->clock->now(),
        );
        $this->mapWithDeactivatedFields = Map::fromArray(
            UuidV4::generateNew(),
            UuidV4::generateNew(),
            [
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 0
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 1,
                ],
                [
                    "monk" => [
                        "id" => 2,
                        "team" => "red"
                    ],
                    "color" => "red",
                    "row" => 0,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 5,
                ],
                [
                    "monk" => [
                        "id" => 4,
                        "team" => "green"
                    ],
                    "color" => "green",
                    "row" => 0,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 0,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 0,
                    "column" => 15,
                ],
                [
                    "monk" => [
                        "id" => 1,
                        "team" => "red"
                    ],
                    "color" => "red",
                    "row" => 1,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 1,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 1,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 3,
                ],
                [
                    "monk" => [
                        "id" => 3,
                        "team" => "green"
                    ],
                    "color" => "green",
                    "row" => 2,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 2,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 2,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 3,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 3,
                    "column" => 14,
                ],
                [
                    "monk" => [
                        "id" => 6,
                        "team" => "blue"
                    ],
                    "color" => "blue",
                    "row" => 4,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 5,
                ],
                [
                    "monk" => [
                        "id" => 5,
                        "team" => "blue"
                    ],
                    "color" => "blue",
                    "row" => 4,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 4,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 4,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "white",
                    "row" => 5,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 5,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 6,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 7,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 14,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 8,
                    "column" => 15,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 0,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 1,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 2,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 3,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 4,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 5,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 6,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 7,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 8,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 9,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 10,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 11,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 12,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 13,
                ],
                [
                    "monk" => null,
                    "color" => "black",
                    "row" => 9,
                    "column" => 14,
                ]
            ],
            $this->clock->now(),
        );
    }

    public function testGetPossibleMoves(): void
    {
        //act
        $moves = $this->map->getPossibleMoves();

        // assert
        self::assertSame(
            [
                1 => [
                    'team' => Team::RED,
                    'moves' => [
                        Direction::NORTH_WEST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_WEST,
                        Direction::NORTH_EAST,
                        Direction::SOUTH_EAST,
                    ],
                ],
                2 => [
                    'team' => Team::BLUE,
                    'moves' => [
                        Direction::NORTH_EAST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_EAST,
                        Direction::NORTH_WEST,
                        Direction::SOUTH_WEST,
                    ],
                ],
                3 => [
                    'team' => Team::RED,
                    'moves' => [
                        Direction::NORTH_WEST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_WEST,
                        Direction::NORTH_EAST,
                        Direction::SOUTH_EAST,
                    ],
                ],
                5 => [
                    'team' => Team::GREEN,
                    'moves' => [
                        Direction::NORTH_EAST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_EAST,
                        Direction::NORTH_WEST,
                    ],
                ],
                4 => [
                    'team' => Team::GREEN,
                    'moves' => [
                        Direction::NORTH_WEST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_WEST,
                        Direction::SOUTH_EAST,
                    ],
                ],
            ],
            $moves
        );
    }

    public function testGetPossibleMovesOnMapWithDeactivatedFields(): void
    {
        //act
        $moves = $this->mapWithDeactivatedFields->getPossibleMoves();

        // assert
        self::assertSame(
            [
                2 => [
                    'team' => Team::RED,
                    'moves' => [
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_EAST,
                        Direction::SOUTH_WEST,
                    ],
                ],
                4 => [
                    'team' => Team::GREEN,
                    'moves' => [
                        Direction::WEST,
                        Direction::SOUTH_EAST,
                        Direction::SOUTH_WEST,
                    ],
                ],
                1 => [
                    'team' => Team::RED,
                    'moves' => [
                        Direction::NORTH_WEST,
                        Direction::EAST,
                        Direction::SOUTH_WEST,
                        Direction::NORTH_EAST,
                        Direction::SOUTH_EAST,
                    ],
                ],
                3 => [
                    'team' => Team::GREEN,
                    'moves' => [
                        Direction::NORTH_EAST,
                        Direction::WEST,
                        Direction::EAST,
                        Direction::SOUTH_EAST,
                        Direction::NORTH_WEST,
                        Direction::SOUTH_WEST,
                    ],
                ],
                6 => [
                    'team' => Team::BLUE,
                    'moves' => [
                        Direction::NORTH_EAST,
                        Direction::EAST,
                    ],
                ],
                5 => [
                    'team' => Team::BLUE,
                    'moves' => [
                        Direction::NORTH_EAST,
                        Direction::WEST,
                        Direction::NORTH_WEST,
                    ],
                ],
            ],
            $moves
        );
    }

    public function testPrepareMoveList(): void
    {
        // arrange
        $moves = [
            '1' => new Move('1', Direction::WEST->value),
            '2' => new Move('2', Direction::EAST->value),
            '3' => new Move('3', Direction::NORTH_WEST->value),
            '4' => new Move('4', Direction::SOUTH_EAST->value),
            '5' => new Move('5', Direction::EAST->value),
        ];

        // act
        $moveList = $this->map->prepareMoveList($moves);

        // assert
        self::assertSame([
            1 => [
                'id' => 1,
                'team' => 'red',
                'moveFrom' => [
                    'row' => '1',
                    'column' => '8',
                ],
                'moveTo' => [
                    'row' => '1',
                    'column' => '7',
                ],
                'direction' => 'W',
            ],
            2 => [
                'id' => 2,
                'team' => 'blue',
                'moveFrom' => [
                    'row' => '2',
                    'column' => '5',
                ],
                'moveTo' => [
                    'row' => '2',
                    'column' => '6',
                ],
                'direction' => 'E',
            ],
            3 => [
                'id' => 3,
                'team' => 'red',
                'moveFrom' => [
                    'row' => '3',
                    'column' => '13',
                ],
                'moveTo' => [
                    'row' => '2',
                    'column' => '13',
                ],
                'direction' => 'NW',
            ],
            5 => [
                    'id' => 5,
                    'team' => 'green',
                    'moveFrom' => [
                        'row' => '4',
                        'column' => '10',
                    ],
                    'moveTo' => [
                        'row' => '4',
                        'column' => '11',
                    ],
                    'direction' => 'E',
            ],
            4 => [
                    'id' => 4,
                    'team' => 'green',
                    'moveFrom' => [
                        'row' => '5',
                        'column' => '9',
                    ],
                    'moveTo' => [
                        'row' => '6',
                        'column' => '10',
                    ],
                    'direction' => 'SE',
            ],
        ], $moveList);
    }
}
