<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\ChatSessionQuery as ChatSessionQueryInterface;
use App\Game\Application\Query\ChatSessionQuery\ChatSession;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

readonly class ChatSessionQuery implements ChatSessionQueryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @throws ChatSessionNotFoundException
     * @throws Exception
     */
    public function getSession(Uuid $id): ChatSession
    {
        /** @var array{
         *     id: string,
         *     messages: string,
         *     created_at: string,
         *     updated_at: string,
         * }|false $session
         */
        $session = $this->connection->fetchAssociative('
            SELECT 
                id,
                messages,
                created_at,
                updated_at
            FROM chat_sessions
            WHERE id = :id;
        ', ['id' => $id]);

        if (!$session) {
            throw ChatSessionNotFoundException::byId($id);
        }

        return ChatSession::fromArray($session);
    }

    /**
     *  @return array{
     *      id: string,
     *      created_at: string,
     *      updated_at: string,
     *  }[]
     *
     * @throws Exception
     */
    public function allWithDates(): array
    {
        /** @var array{
         *      id: string,
         *      created_at: string,
         *      updated_at: string,
         * }[] $sessionsWithDates */
        $sessionsWithDates = $this->connection->fetchAllAssociative(
            <<<SQL
                SELECT id, created_at, updated_at
                FROM chat_sessions
                ORDER BY updated_at DESC
            SQL
        );

        return $sessionsWithDates;
    }
}
