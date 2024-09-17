<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase\CreateSession;

use App\Core\Domain\Uuid;
use DateTimeImmutable;

readonly class Command
{
    public function __construct(
        public Uuid $sessionId,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
