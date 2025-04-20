<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402111314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contingut DROP FOREIGN KEY FK_C6751388CCB2D02F');
        $this->addSql('DROP INDEX IDX_C6751388CCB2D02F ON contingut');
        $this->addSql('ALTER TABLE contingut CHANGE entradaid entradaid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contingut ADD CONSTRAINT FK_C6751388F107D39E FOREIGN KEY (entradaid_id) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_C6751388F107D39E ON contingut (entradaid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contingut DROP FOREIGN KEY FK_C6751388F107D39E');
        $this->addSql('DROP INDEX IDX_C6751388F107D39E ON contingut');
        $this->addSql('ALTER TABLE contingut CHANGE entradaid_id entradaid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contingut ADD CONSTRAINT FK_C6751388CCB2D02F FOREIGN KEY (entradaid) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_C6751388CCB2D02F ON contingut (entradaid)');
    }
}
