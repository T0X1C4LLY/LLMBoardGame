<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231112181116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'winning_team added to turns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE turns ADD winning_team VARCHAR(255)');
        $this->addSql("UPDATE turns SET winning_team = 'red'");
        $this->addSql('ALTER TABLE turns ALTER COLUMN winning_team SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE turns DROP winning_team');
    }
}


