<?php

declare(strict_types=1);

namespace App\Game\Application\Query;

use App\Game\Application\Query\GlobalStatisticsQuery\GlobalStatistics;

interface GlobalStatisticsQuery
{
    public function getAll(): GlobalStatistics;
}
