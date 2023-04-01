<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126173525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE templatetype templatetype VARCHAR(255) DEFAULT NULL, CHANGE activada activada TINYINT(1) DEFAULT NULL, CHANGE finalitzada finalitzada TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog CHANGE title title VARCHAR(255) NOT NULL, CHANGE templatetype templatetype VARCHAR(255) NOT NULL, CHANGE activada activada TINYINT(1) NOT NULL, CHANGE finalitzada finalitzada TINYINT(1) NOT NULL');
    }
}
