<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase;

use App\Game\Application\UseCase\CreateMap\Command;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Exception\InvalidCoordinatesException;
use App\Game\Domain\Repository\MapRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateMap
{
    public function __construct(
        private MapRepository $mapRepository,
    ) {
    }

    /**
     * @param CreateMap\Command $command
     *
     * @throws InvalidCoordinatesException
     */
    public function __invoke(Command $command): void
    {
        $this->mapRepository->add(
            Map::fromArray(
                $command->mapId,
                $command->sessionId,
                $command->fields,
                $command->createdAt,
            )
        );
    }
}
