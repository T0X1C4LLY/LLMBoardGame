<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Inner;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\SessionStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Repository\SessionStatisticsRepository;
use App\Game\Domain\SessionStatisticsClient as SessionStatisticsClientInterface;

readonly class SessionStatisticsClient implements SessionStatisticsClientInterface
{
    public function __construct(
        private SessionStatisticsRepository $sessionStatisticsRepository,
    ) {
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function addCorrectAnswer(Uuid $sessionId): void
    {
        $sessionStatistics = $this->getSessionStatisticsById($sessionId);
        $sessionStatistics->addCorrectAnswer();
        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function addSemanticallyIncorrectAnswer(Uuid $sessionId): void
    {
        $sessionStatistics = $this->getSessionStatisticsById($sessionId);
        $sessionStatistics->addSemanticallyIncorrectAnswer();
        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function addAnswerWithIncorrectMove(Uuid $sessionId): void
    {
        $sessionStatistics = $this->getSessionStatisticsById($sessionId);
        $sessionStatistics->addAnswerWithIncorrectMove();
        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function addAnswerWithMissingMove(Uuid $sessionId): void
    {
        $sessionStatistics = $this->getSessionStatisticsById($sessionId);
        $sessionStatistics->addAnswerWithMissingMove();
        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    public function setQuantityOfMonksIfNeeded(Uuid $sessionId, int $quantityOfMonks): void
    {
        $sessionStatistics = $this->getSessionStatisticsById($sessionId);
        if (0 !== $sessionStatistics->getQuantityOfMonks()) {
            return;
        }

        $sessionStatistics->setQuantityOfMonks($quantityOfMonks);
        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    private function getSessionStatisticsById(Uuid $sessionId): SessionStatistics
    {
        return $this->sessionStatisticsRepository->getById($sessionId);
    }
}
