<?php

declare(strict_types=1);

namespace App\Game\Domain\Entity;

use App\Core\Domain\Uuid;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\MapElement\Move;
use DateTimeImmutable;

readonly class Turn
{
    /** @param Move[] $moves */
    public function __construct(
        public Uuid $id,
        public Uuid $sessionId,
        public Uuid $currentMapId,
        public array $moves,
        public bool $isFinished,
        public int $gamesInRow,
        public int $numberOfTurn,
        public DateTimeImmutable $createdAt,
        public Team $winningTeam,
    ) {
    }
}
