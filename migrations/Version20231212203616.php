<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212203616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'maps.fields type changed to field_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE maps ALTER fields TYPE JSONB');
        $this->addSql('COMMENT ON COLUMN maps.id IS \'(DC2Type:field_type)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE maps ALTER fields TYPE JSONB');
    }
}

