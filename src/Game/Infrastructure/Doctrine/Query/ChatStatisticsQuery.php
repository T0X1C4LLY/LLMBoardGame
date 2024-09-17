<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\SessionStatisticsQuery as SessionStatisticsQueryInterface;
use App\Game\Application\Query\SessionStatisticsQuery\SessionStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use JsonException;

readonly class ChatStatisticsQuery implements SessionStatisticsQueryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @throws ChatSessionNotFoundException
     * @throws Exception
     * @throws JsonException
     */
    public function getById(Uuid $id): SessionStatistics
    {
        /** @var array{
         *      session_id: string,
         *      messages: string,
         *      model: string,
         *      quantity_of_all_answers: int,
         *      quantity_of_correct_answers: int,
         *      quantity_of_semantically_incorrect_answers: int,
         *      quantity_of_answers_with_incorrect_move: int,
         *      quantity_of_answers_with_missing_move: int,
         *      quantity_of_monks: int,
         * }|false $session
         */
        $session = $this->connection->fetchAssociative('
            SELECT 
                session_id,
                messages,
                model,
                quantity_of_all_answers,
                quantity_of_correct_answers,
                quantity_of_semantically_incorrect_answers,
                quantity_of_answers_with_incorrect_move,
                quantity_of_answers_with_missing_move,
                quantity_of_monks
            FROM session_statistics
            WHERE session_id = :id;
        ', ['id' => $id]);

        if (!$session) {
            throw ChatSessionNotFoundException::byId($id);
        }

        return SessionStatistics::fromArray($session);
    }
}
