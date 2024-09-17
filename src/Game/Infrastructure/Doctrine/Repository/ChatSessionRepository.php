<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\ChatSession;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Repository\ChatSessionRepository as ChatSessionRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

readonly class ChatSessionRepository implements ChatSessionRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function add(ChatSession $session): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($session);
        $entityManager->flush();
    }

    /**
     * @throws ChatSessionNotFoundException
     */
    public function getById(Uuid $sessionId): ChatSession
    {
        $chatSession = $this->managerRegistry->getRepository(ChatSession::class)->find($sessionId);

        if (!$chatSession) {
            throw ChatSessionNotFoundException::byId($sessionId);
        }

        return $chatSession;
    }
}
