<?php

declare(strict_types=1);

namespace App\Game\Application\Query\GlobalStatisticsQuery;

readonly class GlobalStatistics
{
    /**
     * @param SingleStatistic[] $statistics
     */
    private function __construct(
        public array $statistics,
    ) {
    }

    /**
     * @param array<int, array{
     *     codeCounter: array{200: int, 400: int},
     *     requestTime: int,
     *     counter: int,
     * }> $statistics
     */
    public static function fromArray(array $statistics): self
    {
        return new self(
            array_map(static fn(array $statistic) => SingleStatistic::fromArray($statistic), $statistics)
        );
    }
}
