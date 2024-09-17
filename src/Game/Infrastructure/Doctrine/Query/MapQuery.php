<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\MapQuery as MapQueryInterface;
use App\Game\Application\Query\MapQuery\Map;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use JsonException;

readonly class MapQuery implements MapQueryInterface
{
    public function __construct(private Connection $connection, private string $isInDebugMode)
    {
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function getNewestBySessionId(Uuid $sessionId): Map
    {
        /** @var array{
         *      fields: string,
         *      moves: string,
         *      games_in_row: int,
         *      number_of_turn: int,
         * }|false $map */
        $map = $this->connection->fetchAssociative('
            SELECT 
                m.fields, 
                t.moves,
                t.games_in_row,
                t.number_of_turn,
                t.winning_team
            FROM maps AS m
            JOIN turns AS t ON m.session_id = t.session_id AND m.id = t.current_map_id
            WHERE m.session_id = :sessionId
            ORDER BY t.games_in_row DESC, t.number_of_turn DESC
        ', [
            'sessionId' => $sessionId,
        ]);

        if (!$map) {
            if ($this->isInDebugMode === 'true') {
                $rand = random_int(0, 3);

                return match ($rand) {
                    0 => Map::small(),
                    1 => Map::oneInEach(),
                    2 => Map::twoInEach(),
                    3 => Map::threeInEach(),
                };
            }

            return Map::empty();
        }

        /** @var array{
         *      fields: string,
         *      moves: string,
         *      games_in_row: int,
         *      number_of_turn: int,
         *      winning_team: string,
         * } $map */
        return Map::fromArray($map);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function getById(Uuid $mapId): Map
    {
        /** @var array{
         *      fields: string,
         *      moves: string,
         *      games_in_row: int,
         *      number_of_turn: int,
         * }|false $map */
        $map = $this->connection->fetchAssociative('
            SELECT 
                m.fields, 
                t.moves,
                t.games_in_row,
                t.number_of_turn,
                t.winning_team
            FROM maps AS m
            JOIN turns AS t ON m.session_id = t.session_id AND m.id = t.current_map_id
            WHERE m.id = :sessionId;
        ', [
            'sessionId' => $mapId,
        ]);

        /** @var array{
         *      fields: string,
         *      moves: string,
         *      games_in_row: int,
         *      number_of_turn: int,
         *      winning_team: string,
         * } $map */
        return Map::fromArray($map);
    }
}
