<?php

declare(strict_types=1);

namespace App\Game\Application\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\SessionStatisticsQuery\SessionStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;

interface SessionStatisticsQuery
{
    /**
     * @throws ChatSessionNotFoundException
     */
    public function getById(Uuid $id): SessionStatistics;
}
