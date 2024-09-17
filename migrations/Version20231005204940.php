<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005204940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'number_of_turn added to turns table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE turns ADD number_of_turn INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE turns DROP number_of_turn');
    }
}
