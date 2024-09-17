<?php

declare(strict_types=1);

namespace App\Game\Application\Query\MapQuery;

class MapCoordinates
{
    private function __construct(
        public int $row,
        public int $column,
    ) {
    }

    /**
     * @param array{row: int, column: int} $coordinates
     */
    public static function fromArray(array $coordinates): self
    {
        return new self(
            $coordinates['row'],
            $coordinates['column'],
        );
    }

    public static function fromId(int $id): self
    {
        $row = 0;
        $column = 0;

        while ($id > 0) {
            if (0 === $row % 2) {
                if ($id >= 16) {
                    $id -= 16;
                    ++$row;
                    continue;
                }
                $column = $id;
                break;
            }
            if ($id >= 15) {
                $id -= 15;
                ++$row;
                continue;
            }
            $column = $id;
            break;
        }

        return new self(
            $row,
            $column
        );
    }
}
