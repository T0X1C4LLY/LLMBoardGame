<?php

declare(strict_types=1);

namespace App\Game\Application\Query\ChatSessionQuery;

use App\Core\Domain\Uuid;
use App\Core\Infrastructure\Symfony\UuidV4;
use DateTimeImmutable;
use JsonException;

class ChatSession
{
    /**
     * @param array<string, string>[] $messages
     */
    public function __construct(
        public Uuid $id,
        public array $messages,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param array{
     *     id: string,
     *     messages: string,
     *     created_at: string,
     *     updated_at: string,
     * } $session
     *
     * @throws JsonException
     */
    public static function fromArray(array $session): self
    {
        /** @var DateTimeImmutable $createdAt */
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $session['created_at']);
        /** @var DateTimeImmutable $updatedAt */
        $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $session['updated_at']);
        /** @var array<string, string>[] $messages */
        $messages = json_decode($session['messages'], true, 512, JSON_THROW_ON_ERROR);

        return new self(
            UuidV4::fromString($session['id']),
            $messages,
            $createdAt,
            $updatedAt,
        );
    }
}
