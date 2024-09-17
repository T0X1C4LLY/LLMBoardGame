<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Inner;

use App\Core\Domain\Uuid;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\NotEnoughMapsToCompareException;
use App\Game\Domain\MoveRater as MoveRaterInterface;
use App\Game\Domain\Repository\MapRepository;

readonly class MoveRater implements MoveRaterInterface
{
    public function __construct(
        private MapRepository $mapRepository,
    ) {
    }

    public function shouldTipBeSend(Uuid $sessionId, Team $chosenTeam): bool
    {
        try {
            ['firstMap' => $firstMap, 'secondMap' => $secondMap] = $this->mapRepository->findForCompare($sessionId);
        } catch (NotEnoughMapsToCompareException) {
            return false;
        }

        ['conqueringTeam' => $conqueringTeam, 'conqueredTeam' => $conqueredTeam] = $chosenTeam->getFightingTeams();

        $distanceOnFirstMap = $firstMap->getDistanceBetweenTwoTeams($conqueringTeam, $conqueredTeam);
        $distanceOnSecondMap = $secondMap->getDistanceBetweenTwoTeams($conqueringTeam, $conqueredTeam);

        return $distanceOnSecondMap < $distanceOnFirstMap;
    }
}
