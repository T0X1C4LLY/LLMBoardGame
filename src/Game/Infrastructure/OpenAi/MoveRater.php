<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\OpenAi;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient as ChatClientInterface;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\NotEnoughMapsToCompareException;
use App\Game\Domain\MoveRater as MoveRaterInterface;
use App\Game\Domain\Repository\MapRepository;

readonly class MoveRater implements MoveRaterInterface
{
    public function __construct(
        private MapRepository $mapRepository,
        private ChatClientInterface $chatClient,
    ) {
    }

    public function shouldTipBeSend(Uuid $sessionId, Team $chosenTeam): bool
    {
        try {
            ['firstMap' => $firstMap, 'secondMap' => $secondMap] = $this->mapRepository->findForCompare($sessionId);
        } catch (NotEnoughMapsToCompareException) {
            return false;
        }

        return $this->chatClient->rateMoves($firstMap, $secondMap, $chosenTeam);
    }
}
