<?php

declare(strict_types=1);

namespace App\Game\Domain\Entity;

use App\Core\Domain\Uuid;

class SessionStatistics
{
    /**
     * @param string[] $messages
     */
    public function __construct(
        public readonly Uuid $sessionId,
        private array $messages,
        public readonly string $model,
        private int $quantityOfAllAnswers,
        private int $quantityOfCorrectAnswers,
        private int $quantityOfSemanticallyIncorrectAnswers,
        private int $quantityOfAnswersWithIncorrectMove,
        private int $quantityOfAnswersWithMissingMove,
        private int $quantityOfMonks,
    ) {
    }

    public function addCorrectAnswer(): void
    {
        $this->messages[] = 'Correct answer';
        ++$this->quantityOfCorrectAnswers;
        ++$this->quantityOfAllAnswers;
    }

    public function addSemanticallyIncorrectAnswer(): void
    {
        $this->messages[] = 'Semantically incorrect answer';
        ++$this->quantityOfSemanticallyIncorrectAnswers;
        ++$this->quantityOfAllAnswers;
    }

    public function addAnswerWithIncorrectMove(): void
    {
        $this->messages[] = 'Answer had incorrect move';
        ++$this->quantityOfAnswersWithIncorrectMove;
        ++$this->quantityOfAllAnswers;
    }

    public function addAnswerWithMissingMove(): void
    {
        $this->messages[] = 'Answer was missing a move';
        ++$this->quantityOfAnswersWithMissingMove;
        ++$this->quantityOfAllAnswers;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getQuantityOfCorrectAnswers(): int
    {
        return $this->quantityOfCorrectAnswers;
    }

    public function getQuantityOfSemanticallyIncorrectAnswers(): int
    {
        return $this->quantityOfSemanticallyIncorrectAnswers;
    }

    public function getQuantityOfAnswersWithIncorrectMove(): int
    {
        return $this->quantityOfAnswersWithIncorrectMove;
    }

    public function getQuantityOfAnswersWithMissingMove(): int
    {
        return $this->quantityOfAnswersWithMissingMove;
    }

    public function getQuantityOfAllAnswers(): int
    {
        return $this->quantityOfAllAnswers;
    }

    public function getQuantityOfMonks(): int
    {
        return $this->quantityOfMonks;
    }

    public function setQuantityOfMonks(int $quantityOfMonks): void
    {
        $this->quantityOfMonks = $quantityOfMonks;
    }
}
