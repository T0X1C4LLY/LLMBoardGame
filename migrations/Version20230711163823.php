<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711163823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'chat_sessions table created';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE chat_sessions (id UUID NOT NULL, messages JSONB DEFAULT \'[{"role": "system", "content": "We are going to play a game, there are 5 players: nr 1 is in red team, nr 2 and 3 are in blue team, nr 4 and 5 are in green team, You have to move every one of them. The directions are N - north, S - south NW - north-west etc. player 1 can move any direction, player 2 can move any direction, player 3 can move any direction, player 4 can move any direction, player 5 can move S, N, W, E. Move every one of them. Could You please response in this syntax: [1:\\"N\\", 2:\\"S\\",] of course this is an example, please use this info to move every player and use syntax. Do not write any extra information or comments, just moves in given syntax please. "}, {"role": "assistant", "content": "OK"}]\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN chat_sessions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN chat_sessions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN chat_sessions.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE chat_sessions');
    }
}

