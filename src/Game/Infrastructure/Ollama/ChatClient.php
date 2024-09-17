<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Ollama;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient as ChatClientInterface;
use App\Game\Domain\ChatClient\Move;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\ChatRole;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Exception\ModelResponseException;
use App\Game\Domain\Repository\ChatSessionRepository;
use App\Ollama\Domain\Ollama;
use JsonException;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Yaml\Yaml;

readonly class ChatClient implements ChatClientInterface
{
    public function __construct(
        private ChatSessionRepository $chatSessionRepository,
        private ClockInterface $clock,
        private Ollama $ollama,
    ) {
    }

    /**
     * @return array<numeric-string, Move>
     *
     * @throws ModelResponseException
     * @throws ChatSessionNotFoundException
     * @throws JsonException
     */
    public function chat(Uuid $sessionId, string $message): array
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $receivedMessage = $this->sendMessage($sessionId, $message);

        $chatSession->addMessage(ChatRole::ASSISTANT, $receivedMessage, $this->clock->now());

        $this->chatSessionRepository->add($chatSession);

        $trimmedMessage = $this->trimMessageOutOfUnusedData($receivedMessage);

        /** @var array{monk_number: int|string, move_direction: string}[] $moves */
        $moves = Yaml::parse($trimmedMessage);

        $decodedMoves = [];

        if (array_key_exists('moves', $moves)) {
            $moves = array_values($moves);
        }

        foreach ($moves as $move) {
            $decodedMoves[(int) $move['monk_number']] = new Move((string) $move['monk_number'], $move['move_direction']);
        }

        return $decodedMoves;
    }

    public function sendTip(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like Your moves are not getting You closer to victory. Please take it into account in next turns.',
            $this->clock->now()
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }

    /**
     * @throws ChatSessionNotFoundException
     * @throws JsonException
     * @throws ModelResponseException
     */
    private function sendMessage(Uuid $sessionId, string $message): string
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(ChatRole::USER, $message, $this->clock->now());

        return $this->ollama->chat($chatSession->getMessages());
    }

    public function sendInfoAboutError(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like Your last response was invalid, please remember about all the rules given to You',
            $this->clock->now()
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }

    /**
     * @throws ModelResponseException
     * @throws JsonException
     */
    public function rateMoves(Map $firstMap, Map $secondMap, Team $chosenTeam): bool
    {
        $response = $this->ollama->chat($this->prepareMessageFromMaps($firstMap, $secondMap, $chosenTeam));

        /** @var array{
         *     id: string,
         *     object: string,
         *     created: int,
         *     model: string,
         *     choices: array{
         *          index: int,
         *          message: array{
         *              role: string,
         *              content: string,
         *          },
         *          finish_reason: string,
         *     }[],
         *     usage: array{
         *          prompt_token: int,
         *          completion_tokens: int,
         *          total_tokens: int,
         *     }
         * }| array{
         *     error: array{
         *          message: string,
         *          type: string,
         *          param: string|null,
         *          code: string,
         * }} $decodedResponse
         */
        $decodedResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (!array_key_exists('choices', $decodedResponse)) {
            throw new ModelResponseException(sprintf('ChatGPT response with error %s', $decodedResponse['error']['code']));
        }

        return 'true' === $decodedResponse['choices'][0]['message']['content'];
    }

    /**
     * @return array{role: string, content: string}[]
     *
     * @throws JsonException
     */
    private function prepareMessageFromMaps(Map $firstMap, Map $secondMap, Team $chosenTeam): array
    {
        return [
            [
                'role' => ChatRole::USER->value,
                 'content' => sprintf(
                     'Please rate player\'s moves based on the info I\'m going to send You.
                    Goal: Move proper monks so team %s wins
                    Rules: When two monks from different teams are on neighboring fields one beats another, red beats green, green beats blue, blue beats red.
                    Map before moves: %s,
                    Map after moves: %s.
                    Please response with "true" if You think that the moves are good (bring player close to success) or "false" if You think they are not',
                     $chosenTeam->value,
                     json_encode($firstMap->jsonSerialize(), JSON_THROW_ON_ERROR),
                     json_encode($secondMap->jsonSerialize(), JSON_THROW_ON_ERROR),
                 ),
            ],
        ];
    }

    public function sendInfoAboutSemanticIssue(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like Your last response was not a valid YAML, next time please ensure to respond with valid one',
            $this->clock->now()
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }

    private function trimMessageOutOfUnusedData(string $receivedMessage): string
    {
        $start = strpos($receivedMessage, '```yaml');

        if ($start === false) {
            $messageWithoutExtraKey = str_replace([sprintf('-monks:%s', PHP_EOL), sprintf('monks:%s', PHP_EOL), sprintf('optimal_moves:%s', PHP_EOL), sprintf('-optimal_moves:%s', PHP_EOL)], '', $receivedMessage);
            $messageWithSpacesAfterDash = str_replace(['-monk_number:', '-['], ['- monk_number:', '- ['], $messageWithoutExtraKey);
            $messageWithoutExtraSpaces = str_replace(['    - monk_number:', '      move_direction:'], ['- monk_number:', '  move_direction:'], $messageWithSpacesAfterDash);

            if (str_starts_with($messageWithoutExtraSpaces, '[') && str_ends_with($messageWithoutExtraSpaces, '}')) {
                $messageWithoutExtraSpaces .= PHP_EOL . ']';
            }

            if ((str_starts_with($messageWithoutExtraSpaces, '['.PHP_EOL.'{') || str_starts_with($messageWithoutExtraSpaces, '['.PHP_EOL.' {')) && !str_ends_with($messageWithoutExtraSpaces, '}'.PHP_EOL.']')) {
                $messageWithoutExtraSpaces .= '}'. PHP_EOL . ']';
            }

            return $messageWithoutExtraSpaces;
        }

        $end = strlen($receivedMessage) - strrpos($receivedMessage, '```');

        return substr($receivedMessage, ($start + 8), -$end);
    }

    public function sendInfoAboutIncompleteData(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like Your last response does not contained all the necessary moves for monks, please make sure you send move for every monk that can make a move in current turn.',
            $this->clock->now()
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }

    public function sendInfoGameEnd(Uuid $sessionId, Team $teamThatWon, Team $teamThatShouldHaveWon): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $message = sprintf(
            'Unfortunately You lose. The goal was to team: %s win the game but actually team: %s won. Next turns will be a part of a new game, be aware of that.',
            $teamThatShouldHaveWon->value,
            $teamThatWon->value,
        );

        if ($teamThatWon->value === $teamThatShouldHaveWon->value) {
            $message = sprintf('Congrats! You manage to make proper moves and make team %s win this game. Next turns will be a part of a new game, be aware of that.', $teamThatWon->value);
        }

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            $message,
            $this->clock->now(),
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }

    public function sendInfoAboutIncorrectMove(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like In Your last response was an error. You wanted to move a monk in direction it could not move. Please ensure that You pick proper directions for each monk',
            $this->clock->now()
        );

        $this->ollama->chat($chatSession->getMessages());

        $this->chatSessionRepository->add($chatSession);
    }
}
