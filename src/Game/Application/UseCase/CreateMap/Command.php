<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase\CreateMap;

use App\Core\Domain\Uuid;
use DateTimeImmutable;

readonly class Command
{
    /**
     * @param array<array{
     *      row: int,
     *      column: int,
     *      monk: array{
     *           id: int,
     *           team: string,
     *      }|null,
     *      color: string,
     *  }|null> $fields
     */
    public function __construct(
        public Uuid $mapId,
        public array $fields,
        public Uuid $sessionId,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
