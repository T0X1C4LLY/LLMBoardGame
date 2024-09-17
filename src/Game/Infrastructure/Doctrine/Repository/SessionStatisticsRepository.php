<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\SessionStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Repository\SessionStatisticsRepository as SessionStatisticsRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

readonly class SessionStatisticsRepository implements SessionStatisticsRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function add(SessionStatistics $sessionStatistics): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($sessionStatistics);
        $entityManager->flush();
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function getById(Uuid $sessionStatisticsId): SessionStatistics
    {
        $sessionStatistics = $this->managerRegistry->getRepository(SessionStatistics::class)->find($sessionStatisticsId);

        if (!$sessionStatistics) {
            throw ChatSessionNotFoundException::byId($sessionStatisticsId);
        }

        return $sessionStatistics;
    }
}
