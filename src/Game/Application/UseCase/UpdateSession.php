<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase;

use App\Game\Application\UseCase\UpdateSession\Command;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Repository\ChatSessionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpdateSession
{
    public function __construct(
        private ChatSessionRepository $sessionRepository,
    ) {
    }

    /**
     * @param UpdateSession\Command $command
     *
     * @throws ChatSessionNotFoundException
     */
    public function __invoke(Command $command): void
    {
        $chatSession = $this->sessionRepository->getById($command->sessionId);
        $chatSession->addMessages($command->messages, $command->updatedAt);

        $this->sessionRepository->add($chatSession);
    }
}
