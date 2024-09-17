<?php

declare(strict_types=1);

namespace App\Game\Application\UseCase;

use App\Game\Application\UseCase\CreateSession\Command;
use App\Game\Domain\Entity\ChatSession;
use App\Game\Domain\Entity\SessionStatistics;
use App\Game\Domain\Repository\ChatSessionRepository;
use App\Game\Domain\Repository\SessionStatisticsRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSession
{
    public function __construct(
        private ChatSessionRepository $sessionRepository,
        private SessionStatisticsRepository $sessionStatisticsRepository,
        private string $modelName,
    ) {
    }

    /**
     * @param CreateSession\Command $command
     */
    public function __invoke(Command $command): void
    {
        $chatSession = new ChatSession(
            $command->sessionId,
            $this->getMessages(),
            $command->createdAt,
            $command->createdAt,
        );

        $this->sessionRepository->add($chatSession);

        $sessionStatistics = new SessionStatistics(
            $command->sessionId,
            [],
            $this->modelName,
            0,
            0,
            0,
            0,
            0,
            0,
        );

        $this->sessionStatisticsRepository->add($sessionStatistics);
    }

    /**
     * @return array{role: string, content: string}[]
     */
    private function getMessages(): array
    {
        return match ($this->modelName) {
            'gpt-3.5-turbo' => [
                [
                    'role' => 'system',
                    'content' => '
                        Lets play a game.
                        There are some players.
                        Each one have his number and is a member of one of the team: red, green or blue.
                        There are 6 directions E - east, SW - south-west (there is no north and south) etc.
                        I will tell You in which direction players can move and in which team they are.
                        It is going to look like that:
                        Nr 1: red, E,
                        Nr 2: blue, SE, SW.
                        Use this info to move every player and use given syntax: {"1":"SE", "2":"E"}.
                        You can choose only one direction per player.
                        Please sort them ascending by player number.
                        Please do not write about players that cannot move.
                        Do not write any extra information or comments.
                        Response with "OK" if You understood everything.
                    ',
                ],
            ],
            'phi3' => [
                [
                    'role' => 'system',
                    'content' => 'Your whole response should be in valid YAML format, write no extra commentary, explanation or description',
                ],
                [
                    'role' => 'system',
                    'content' => 'Generate response detailing the optimal moves for a turn in a hexagonal-grid game involving teams red, green, and blue, with units called monks. Each monk can move in six directions: NE, NW, SE, SW, E, W. The goal is to assist the red team in winning. Given the current turn s possibilities: Monk Nr 1 from the red team can move SW or SE; Monk Nr 2 from the green team can move SE or SW; Monk Nr 3 from the blue team can move E or W. Only a single, optimal move should be determined for each monk, reflecting in a YAML format as an array of objects, each specifying \"monk_number\" and \"move_direction\". Your whole response should be in valid YAML format, write no extra commentary, explanation or description. Do not add any note.',
                ],
            ],
            'llama3' => [
                [
                    'role' => 'system',
                    'content' => 'Generate response detailing the optimal moves for a turn in a hexagonal-grid game involving teams red, green, and blue, with units called monks. Each monk can move in six directions: NE, NW, SE, SW, E, W. The goal is to assist the red team in winning. Given the current turn s possibilities: Monk Nr 1 from the red team can move SW or SE; Monk Nr 2 from the green team can move SE or SW; Monk Nr 3 from the blue team can move E or W. Only a single, optimal move should be determined for each monk, reflecting in a YAML format as an array of objects, each specifying \"monk_number\" and \"move_direction\" in format - {\"monk_number\", \"move_direction\"}. Your whole response should be in valid YAML format, write no extra commentary, explanation or description. Do not add any note.',
                ],
            ],
            'llama2:13b' => [
                [
                    'role' => 'system',
                    'content' => 'Generate response detailing the optimal moves for a turn in a hexagonal-grid game involving teams red, green, and blue, with units called monks. Each monk can move in six directions: NE, NW, SE, SW, E, W. The goal is to assist the red team in winning. Given the current turn s possibilities: Monk Nr 1 from the red team can move SW or SE; Monk Nr 2 from the green team can move SE or SW; Monk Nr 3 from the blue team can move E or W. Only a single, optimal move should be determined for each monk, reflecting in a JSON format as an array of objects, each specifying \"monk_number\" and \"move_direction\". Your whole response should be in valid JSON format, write no extra commentary, explanation or description. Do not add any note.',
                ],
            ],
            default => [
                [
                    'role' => 'system',
                    'content' => '
                        Generate response detailing the optimal moves for a turn in a hexagonal-grid game involving teams red, green, and blue, with units called monks.
                        Each monk can move in six directions: NE, NW, SE, SW, E, W.
                        The goal is to assist the red team in winning.
                        Only a single, optimal move should be determined for each monk, reflecting in a YAML format as a single array of objects, each specifying "monk_number" and "move_direction.
                        Your whole response should be in valid YAML format, write no extra commentary, explanation or description.
                    ',
                ],
            ],
        };
    }
}
