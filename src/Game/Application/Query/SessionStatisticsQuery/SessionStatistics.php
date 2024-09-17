<?php

declare(strict_types=1);

namespace App\Game\Application\Query\SessionStatisticsQuery;

use App\Core\Domain\Uuid;
use App\Core\Infrastructure\Symfony\UuidV4;
use JsonException;

readonly class SessionStatistics
{
    /**
     * @param string[] $messages
     */
    public function __construct(
        public Uuid $sessionId,
        public array $messages,
        public string $model,
        public int $quantityOfAllAnswers,
        public int $quantityOfCorrectAnswers,
        public int $quantityOfSemanticallyIncorrectAnswers,
        public int $quantityOfAnswersWithIncorrectMove,
        public int $quantityOfAnswersWithMissingMove,
        public int $quantityOfMonks,
    ) {
    }

    /**
     * @param array{
     *     session_id: string,
     *     messages: string,
     *     model: string,
     *     quantity_of_all_answers: int,
     *     quantity_of_correct_answers: int,
     *     quantity_of_semantically_incorrect_answers: int,
     *     quantity_of_answers_with_incorrect_move: int,
     *     quantity_of_answers_with_missing_move: int,
     *     quantity_of_monks: int,
     * } $session
     *
     * @throws JsonException
     */
    public static function fromArray(array $session): self
    {
        /** @var string[] $messages */
        $messages = json_decode($session['messages'], true, 512, JSON_THROW_ON_ERROR);

        return new self(
            UuidV4::fromString($session['session_id']),
            $messages,
            $session['model'],
            $session['quantity_of_all_answers'],
            $session['quantity_of_correct_answers'],
            $session['quantity_of_semantically_incorrect_answers'],
            $session['quantity_of_answers_with_incorrect_move'],
            $session['quantity_of_answers_with_missing_move'],
            $session['quantity_of_monks'],
        );
    }
}
