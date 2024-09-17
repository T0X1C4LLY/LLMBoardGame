<?php

declare(strict_types=1);

namespace App\Game\Domain\Repository;

use App\Game\Domain\Entity\Turn;

interface TurnRepository
{
    public function add(Turn $turn): void;
}
