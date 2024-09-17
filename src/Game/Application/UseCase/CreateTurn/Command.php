<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase\CreateTurn;

use App\Core\Domain\Uuid;
use App\Game\Domain\Enum\Team;
use DateTimeImmutable;

readonly class Command
{
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
     *  }[] $moves
     */
    public function __construct(
        public Uuid $turnId,
        public Uuid $mapId,
        public Uuid $sessionId,
        public array $moves,
        public int $turnNumber,
        public int $gamesInRow,
        public bool $isFinished,
        public DateTimeImmutable $createdAt,
        private string $winningTeam,
    ) {
    }

    public function getWinningTeam(): Team
    {
        return Team::from($this->winningTeam);
    }
}
