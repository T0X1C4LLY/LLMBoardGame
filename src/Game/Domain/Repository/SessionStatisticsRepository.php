<?php

declare(strict_types=1);

namespace App\Game\Domain\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\SessionStatistics;
use App\Game\Domain\Exception\ChatSessionNotFoundException;

interface SessionStatisticsRepository
{
    public function add(SessionStatistics $sessionStatistics): void;

    /**
     * @throws ChatSessionNotFoundException
     */
    public function getById(Uuid $sessionStatisticsId): SessionStatistics;
}
