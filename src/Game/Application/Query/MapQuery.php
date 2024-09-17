<?php

declare(strict_types=1);

namespace App\Game\Application\Query;

use App\Core\Domain\Uuid;
use App\Game\Application\Query\MapQuery\Map;

interface MapQuery
{
    public function getNewestBySessionId(Uuid $sessionId): Map;

    public function getById(Uuid $mapId): Map;
}
