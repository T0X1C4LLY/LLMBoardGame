<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230722180823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'chat_session.id type changed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_sessions ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN chat_sessions.id IS \'(DC2Type:uuid_type)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_sessions ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN chat_sessions.id IS \'(DC2Type:uuid)\'');
    }
}

