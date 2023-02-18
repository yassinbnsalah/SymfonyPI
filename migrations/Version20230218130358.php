<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218130358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordennance ADD rendez_vous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ordennance ADD CONSTRAINT FK_363B6D2291EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_363B6D2291EF7EAA ON ordennance (rendez_vous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordennance DROP FOREIGN KEY FK_363B6D2291EF7EAA');
        $this->addSql('DROP INDEX UNIQ_363B6D2291EF7EAA ON ordennance');
        $this->addSql('ALTER TABLE ordennance DROP rendez_vous_id');
    }
}
