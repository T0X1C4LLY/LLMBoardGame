<?php

declare(strict_types=1);

namespace App\Game\Domain\Repository;

use App\Core\Domain\Uuid;
use App\Game\Domain\Entity\Map;

interface MapRepository
{
    public function add(Map $map): void;

    /**
     * @return array{firstMap: Map, secondMap: Map}
     */
    public function findForCompare(Uuid $sessionId): array;
}
