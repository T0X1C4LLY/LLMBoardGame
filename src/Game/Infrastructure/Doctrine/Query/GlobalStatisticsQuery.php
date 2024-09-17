<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Doctrine\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\GlobalStatisticsQuery as GlobalStatisticsQueryInterface;
use App\Game\Application\Query\GlobalStatisticsQuery\GlobalStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use JsonException;

readonly class GlobalStatisticsQuery implements GlobalStatisticsQueryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @throws ChatSessionNotFoundException
     * @throws Exception
     * @throws JsonException
     */
    public function getAll(): GlobalStatistics
    {
        $allFromDirectory = array_filter(scandir('../stats'), function($item) {
            return !is_dir('../stats/' . $item);
        });

        $files = array_diff($allFromDirectory, ['..', '.']);

        $statistics = [];

        foreach ($files as $fileName) {
            $sessionId = $this->getSessionId($fileName);

            $sessionInfo = $this->connection->fetchOne(
                <<<SQL
                    SELECT messages
                    FROM chat_sessions
                    WHERE id = :sessionId
                SQL,
                [
                    'sessionId' => $sessionId
                ]
            );

            $decodedInfo = json_decode($sessionInfo, true, 512, JSON_THROW_ON_ERROR);

            $file = fopen(sprintf('../stats/%s', $fileName), 'rb');

            while (($line = fgetcsv($file)) !== FALSE) {
                $values = str_getcsv($line[0], ';');

                if (!array_key_exists($values[0], $statistics)) {
                    $statistics[$values[0]] = [
                        'codeCounter' => [
                            200 => (int) ($values[1] === '200'),
                            400 => (int) ($values[1] === '400'),
                        ],
                        'requestTime' => (int) $values[2],
                        'counter' => 1,
                    ];

                    continue;
                }

                $statistics[$values[0]] = [
                    'codeCounter' => [
                        200 => ($statistics[$values[0]]['codeCounter'][200] + ((int) ($values[1] === '200'))),
                        400 => ($statistics[$values[0]]['codeCounter'][400] + ((int) ($values[1] === '400'))),
                    ],
                    'requestTime' => ($statistics[$values[0]]['requestTime'] + ((int) $values[2])),
                    'counter' => ++$statistics[$values[0]]['counter'],
                ];
            }

            fclose($file);
        }

        return GlobalStatistics::fromArray($statistics);
    }

    private function getSessionId(string $fileName): string
    {
        return substr($fileName, 0, strpos($fileName, '.csv'));
    }
}
