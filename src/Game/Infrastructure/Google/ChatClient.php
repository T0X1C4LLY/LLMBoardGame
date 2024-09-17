<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Google;

use App\Core\Domain\Uuid;
use App\Game\Domain\ChatClient as ChatClientInterface;
use App\Game\Domain\Entity\Map;
use App\Game\Domain\Enum\Team;
use Pj8912\PhpBardApi\Bard;

readonly class ChatClient implements ChatClientInterface
{
    public function __construct(
    ) {
    }

    public function chat(Uuid $sessionId, string $message): array
    {
        $bard = new Bard();
        $input_text = '
            Lets play a game.
            There are some players.
            Each one have his number and is a member of one of the team: red, green or blue.
            There are 6 directions E - east, SW - south-west (there is no north and south) etc.
            I will tell You in which direction players can move and in which team they are.
            It is going to look like that:
            Nr 1: red, E, 
            Nr 2: blue, SE, SW. 
            Use this info to move every player and use JS syntax: {"1":"SE", "2":"E"} (only number and direction).
            You can choose only one direction per player.
            Sort them ascending by player number.
            Move every player (make exception for players that cannot move).
            Do not write any extra information or comments.
            Possible moves: 
        ';
        $input_text_v2 = '
            Lets play a game.
            There are players called monks.
            Every monk has a number and is a member of one of the teams: red, green or blue.
            There are 6 directions E - east, SW - south-west etc.(there is no north and south).
            You will receive all possible directions that each monk can move and in which team they are and which team should win.
            The goal is to move monk so a given team wins.
            You can choose only one direction per monk.
            Sort them ascending by monk number.
            Move every monk (make exception for players that cannot move).
            In response use JSON format where monk number(cast it to string) is key and direction is a value and do not write any extra information or comments.
            Possible moves: 
        ';
        $result = $bard->get_answer($input_text_v2.$message);

        return $result['choices'][0]['content'][0];
    }

    public function sendTip(Uuid $sessionId): void
    {
    }

    public function sendInfoAboutError(Uuid $sessionId): void
    {
    }

    public function rateMoves(Map $firstMap, Map $secondMap, Team $chosenTeam): bool
    {
        return false;
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
