<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\OpenAi;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient as ChatClientInterface;
use App\Game\Domain\ChatClient\Move;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\ChatRole;
use App\Game\Domain\Enum\Team;
use App\Game\Domain\Exception\ChatSessionNotFoundException;
use App\Game\Domain\Exception\ModelResponseException;
use App\Game\Domain\Repository\ChatSessionRepository;
use JsonException;
use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\Clock\ClockInterface;

readonly class ChatClient implements ChatClientInterface
{
    public function __construct(
        private ChatSessionRepository $chatSessionRepository,
        private OpenAi $openAi,
        private ClockInterface $clock,
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

        $chatSession->addMessage(ChatRole::USER, $message, $this->clock->now());

        $receivedMessage = $this->sendMessage($sessionId, $message);

        $chatSession->addMessage(ChatRole::ASSISTANT, $receivedMessage, $this->clock->now());

        $this->chatSessionRepository->add($chatSession);

        /** @var array<numeric-string, string> $moves */
        $moves = json_decode($receivedMessage, true, 512, JSON_THROW_ON_ERROR);

        $decodedMoves = [];

        foreach ($moves as $id => $direction) {
            $decodedMoves[$id] = new Move((string) $id, $direction);
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

        $this->openAi->chat([
            'model' => $_ENV['CHAT_VERSION'],
            'messages' => $chatSession->getMessages(),
            'temperature' => 1.0,
            'max_tokens' => 250,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

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

        $response = $this->openAi->chat([
            'model' => $_ENV['CHAT_VERSION'],
            'messages' => $chatSession->getMessages(),
            'temperature' => 1.0,
            'max_tokens' => 250,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        if (is_bool($response)) {
            throw new ModelResponseException('Response from chat GPT was invalid');
        }

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

        return $decodedResponse['choices'][0]['message']['content'];
    }

    public function sendInfoAboutError(Uuid $sessionId): void
    {
        $chatSession = $this->chatSessionRepository->getById($sessionId);

        $chatSession->addMessage(
            ChatRole::SYSTEM,
            'It looks like Your last response was invalid, please remember about all the rules given to You',
            $this->clock->now()
        );

        $this->openAi->chat([
            'model' => $_ENV['CHAT_VERSION'],
            'messages' => $chatSession->getMessages(),
            'temperature' => 1.0,
            'max_tokens' => 250,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $this->chatSessionRepository->add($chatSession);
    }

    /**
     * @throws ModelResponseException
     * @throws JsonException
     */
    public function rateMoves(Map $firstMap, Map $secondMap, Team $chosenTeam): bool
    {
        $response = $this->openAi->chat([
            'model' => $_ENV['CHAT_VERSION'],
            'messages' => $this->prepareMessageFromMaps($firstMap, $secondMap, $chosenTeam),
            'temperature' => 1.0,
            'max_tokens' => 250,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        if (is_bool($response)) {
            throw new ModelResponseException('Response from chat GPT was invalid');
        }

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
     * @return array<string, string>
     *
     * @throws JsonException
     */
    private function prepareMessageFromMaps(Map $firstMap, Map $secondMap, Team $chosenTeam): array
    {
        return [
            ChatRole::USER->value => sprintf(
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
        ];
    }

    public function sendInfoAboutSemanticIssue(Uuid $sessionId): void
    {
        // TODO: Implement sendInfoAboutSemanticIssue() method.
    }

    public function sendInfoAboutIncompleteData(Uuid $sessionId): void
    {
        // TODO: Implement sendInfoAboutIncompleteData() method.
    }

    public function sendInfoGameEnd(Uuid $sessionId, Team $teamThatWon, Team $teamThatShouldHaveWon): void
    {
        // TODO: Implement sendInfoGameEnd() method.
    }

    public function sendInfoAboutIncorrectMove(Uuid $sessionId): void
    {
        // TODO: Implement sendInfoAboutIncorrectMove() method.
    }
}
