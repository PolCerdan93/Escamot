<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312100041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog DROP imghoritzontal1, DROP imghoritzontal2, DROP imgvertical1, DROP content1, DROP content2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog ADD imghoritzontal1 VARCHAR(255) DEFAULT NULL, ADD imghoritzontal2 VARCHAR(255) DEFAULT NULL, ADD imgvertical1 VARCHAR(255) DEFAULT NULL, ADD content1 TEXT DEFAULT NULL, ADD content2 TEXT DEFAULT NULL');
    }
}
