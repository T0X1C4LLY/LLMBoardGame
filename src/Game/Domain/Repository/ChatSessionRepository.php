<?php

declare(strict_types=1);

namespace App\Game\Domain\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\ChatSession;
use App\Game\Domain\Exception\ChatSessionNotFoundException;

interface ChatSessionRepository
{
    public function add(ChatSession $session): void;

    /**
     * @throws ChatSessionNotFoundException
     */
    public function getById(Uuid $sessionId): ChatSession;
}
