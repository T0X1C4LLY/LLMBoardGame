<?php

declare(strict_types=1);

namespace App\Game\Application\Query\GlobalStatisticsQuery;

readonly class SingleStatistic
{
    /**
     * @param array{200: int, 400: int} $codeCounter
     */
    private function __construct(
        public array $codeCounter,
        public int $requestTime,
        public int $counter,
    ) {
    }

    /**
     * @param array{
     *      codeCounter: array{200: int, 400: int},
     *      requestTime: int,
     *      counter: int,
     *  } $statistics
     */
    public static function fromArray(array $statistics): self
    {
        return new self(
            $statistics['codeCounter'],
            $statistics['requestTime'],
            $statistics['counter'],
        );
    }

    public function getMeanTimeInMilliseconds(): float
    {
        return $this->requestTime / $this->counter;
    }

    public function getMeanTimeInSeconds(): float
    {
        return $this->getMeanTimeInMilliseconds() / 1000;
    }
}
