<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase\UpdateSession;

use App\Core\Domain\Uuid;
use DateTimeImmutable;

readonly class Command
{
    /**
     * @param array{role: string, content: string}[] $messages
     */
    public function __construct(
        public Uuid $sessionId,
        public array $messages,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
