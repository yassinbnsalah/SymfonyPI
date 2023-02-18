<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218131350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, seance_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, diet_note VARCHAR(255) NOT NULL, INDEX IDX_D499BFF6E3797A94 (seance_id), INDEX IDX_D499BFF681C06096 (activity_id), INDEX IDX_D499BFF63C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, niveau VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF63C105691 FOREIGN KEY (coach_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous CHANGE hour_rv hour_rv TIME NOT NULL, CHANGE hour_passage_rv hour_passage_rv TIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6E3797A94');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF681C06096');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF63C105691');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE seance');
        $this->addSql('ALTER TABLE rendez_vous CHANGE hour_rv hour_rv TIME DEFAULT NULL, CHANGE hour_passage_rv hour_passage_rv TIME DEFAULT NULL');
    }
}
