<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323152234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contingut (id INT AUTO_INCREMENT NOT NULL, entradaid_id INT NOT NULL, contingut VARCHAR(255) NOT NULL, ordre INT NOT NULL, INDEX IDX_C6751388F107D39E (entradaid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contingut ADD CONSTRAINT FK_C6751388F107D39E FOREIGN KEY (entradaid_id) REFERENCES blog (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contingut');
    }
}
