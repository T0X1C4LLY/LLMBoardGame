<?php

declare(strict_types=1);

namespace App\Game\Application\Query\MapQuery;

class Monk
{
    private function __construct(
        public int $id,
        public string $team,
    ) {
    }

    /**
     * @param array{id: int, team: string} $monk
     */
    public static function fromArray(array $monk): self
    {
        return new self(
            $monk['id'],
            $monk['team'],
        );
    }
}
