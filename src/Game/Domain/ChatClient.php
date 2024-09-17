<?php

declare(strict_types=1);

namespace App\Game\Domain;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient\Move;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\Team;

interface ChatClient
{
    /**
     * @return array<numeric-string, Move>
     */
    public function chat(Uuid $sessionId, string $message): array;

    public function sendTip(Uuid $sessionId): void;

    public function sendInfoAboutError(Uuid $sessionId): void;

    public function rateMoves(Map $firstMap, Map $secondMap, Team $chosenTeam): bool;

    public function sendInfoAboutSemanticIssue(Uuid $sessionId): void;

    public function sendInfoAboutIncompleteData(Uuid $sessionId): void;

    public function sendInfoGameEnd(Uuid $sessionId, Team $teamThatWon, Team $teamThatShouldHaveWon): void;

    public function sendInfoAboutIncorrectMove(Uuid $sessionId): void;
}
