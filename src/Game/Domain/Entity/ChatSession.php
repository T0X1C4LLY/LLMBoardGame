<?php

declare(strict_types=1);

namespace App\Game\Domain\Entity;

use App\Core\Domain\Uuid;
use App\Game\Domain\Enum\ChatRole;
use DateTimeImmutable;

class ChatSession
{
    /**
     * @param array{role: string, content: string}[] $messages
     */
    public function __construct(
        public readonly Uuid $id,
        public array $messages,
        public readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public function addMessage(ChatRole $role, string $message, DateTimeImmutable $updatedAt): void
    {
        $this->messages[] = [
            'role' => $role->value,
            'content' => $message,
        ];
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array{role: string, content: string}[] $messages
     */
    public function addMessages(array $messages, DateTimeImmutable $updatedAt): void
    {
        $this->messages = array_merge($this->messages, $messages);
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return array{role: string, content: string}[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
