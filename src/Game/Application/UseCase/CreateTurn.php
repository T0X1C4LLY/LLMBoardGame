<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase;

use App\Game\Application\UseCase\CreateTurn\Command;
use App\Game\Domain\Entity\Turn;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\MapElement\Move;
use App\Game\Domain\Repository\TurnRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateTurn
{
    public function __construct(
        private TurnRepository $mapRepository,
    ) {
    }

    /**
     * @param CreateTurn\Command $command
     *
     * @throws InvalidCoordinatesException
     */
    public function __invoke(Command $command): void
    {
        $this->mapRepository->add(
            new Turn(
                $command->turnId,
                $command->sessionId,
                $command->mapId,
                array_map(
                    static fn (array $move): Move => Move::fromArray($move),
                    $command->moves,
                ),
                $command->isFinished,
                $command->gamesInRow,
                $command->turnNumber,
                $command->createdAt,
                $command->getWinningTeam(),
            )
        );
    }
}
