<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Repository;

use App\Game\Domain\Entity\Turn;
use App\Game\Domain\Repository\TurnRepository as TurnRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

readonly class TurnRepository implements TurnRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function add(Turn $turn): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($turn);
        $entityManager->flush();
    }
}
