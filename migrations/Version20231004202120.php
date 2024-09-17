<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231004202120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'maps and turn added';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE maps (id UUID NOT NULL, session_id UUID NOT NULL, fields JSONB DEFAULT \'[]\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN maps.id IS \'(DC2Type:uuid_type)\'');
        $this->addSql('COMMENT ON COLUMN maps.session_id IS \'(DC2Type:uuid_type)\'');
        $this->addSql('COMMENT ON COLUMN maps.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE turns (id UUID NOT NULL, session_id UUID NOT NULL, current_map_id UUID NOT NULL, moves JSONB DEFAULT \'[]\' NOT NULL, is_finished BOOLEAN NOT NULL, games_in_row INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN turns.id IS \'(DC2Type:uuid_type)\'');
        $this->addSql('COMMENT ON COLUMN turns.session_id IS \'(DC2Type:uuid_type)\'');
        $this->addSql('COMMENT ON COLUMN turns.current_map_id IS \'(DC2Type:uuid_type)\'');
        $this->addSql('COMMENT ON COLUMN turns.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE maps');
        $this->addSql('DROP TABLE turns');
    }
}

