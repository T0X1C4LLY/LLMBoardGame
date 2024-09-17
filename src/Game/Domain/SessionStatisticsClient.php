<?php

declare(strict_types=1);

namespace App\Game\Domain;

use App\Core\Domain\Uuid;

interface SessionStatisticsClient
{
    public function addCorrectAnswer(Uuid $sessionId): void;

    public function addSemanticallyIncorrectAnswer(Uuid $sessionId): void;

    public function addAnswerWithIncorrectMove(Uuid $sessionId): void;

    public function addAnswerWithMissingMove(Uuid $sessionId): void;

    public function setQuantityOfMonksIfNeeded(Uuid $sessionId, int $quantityOfMonks): void;
}
