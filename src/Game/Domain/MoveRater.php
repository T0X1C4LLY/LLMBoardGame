<?php

declare(strict_types=1);

namespace App\Game\Domain;

use App\Core\Domain\Uuid;
use App\Game\Domain\Enum\Team;

interface MoveRater
{
    public function shouldTipBeSend(Uuid $sessionId, Team $chosenTeam): bool;
}
