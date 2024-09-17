<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230815201842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'default message to chat changed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_sessions ALTER messages SET DEFAULT \'[{"role": "system", "content": "Lets play a game. There are some players. Each one have his number and is a member of one of the team: red, green or blue. There are 6 directions E - east, SW - south-west (there is no north and south) etc. I will tell You in which direction players can move and in which team they are. It is going to look like that: Nr 1: red, E, Nr 2: blue, SE, SW. Use this info to move every player and use given syntax: [1:\\"SE\\", 2:\\"E\\"]. Do not write any extra information or comments. Response with \\"OK\\" if You understood everything."}, {"role": "assistant", "content": "OK"}]\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_sessions ALTER messages SET DEFAULT \'[{"role": "system", "content": "We are going to play a game, there are 5 players: nr 1 is in red team, nr 2 and 3 are in blue team, nr 4 and 5 are in green team, You have to move every one of them. The directions are N - north, S - south NW - north-west etc. player 1 can move any direction, player 2 can move any direction, player 3 can move any direction, player 4 can move any direction, player 5 can move S, N, W, E. Move every one of them. Could You please response in this syntax: [1:\\"N\\", 2:\\"S\\",] of course this is an example, please use this info to move every player and use syntax. Do not write any extra information or comments, just moves in given syntax please. "}, {"role": "assistant", "content": "OK"}]\'');
    }
}

