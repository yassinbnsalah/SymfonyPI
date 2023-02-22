<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221144328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medicament (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordennance (id INT AUTO_INCREMENT NOT NULL, rendez_vous_id INT DEFAULT NULL, dateordenance DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_363B6D2291EF7EAA (rendez_vous_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordennance_ligne (id INT AUTO_INCREMENT NOT NULL, medicament_id INT DEFAULT NULL, ordennance_id INT DEFAULT NULL, qunatite INT NOT NULL, INDEX IDX_F1C2C65CAB0D61F7 (medicament_id), INDEX IDX_F1C2C65C3CD0AB46 (ordennance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, seance_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, diet_note VARCHAR(255) NOT NULL, salle VARCHAR(15) NOT NULL, date_planning DATE NOT NULL, INDEX IDX_D499BFF6E3797A94 (seance_id), INDEX IDX_D499BFF681C06096 (activity_id), INDEX IDX_D499BFF63C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, niveau VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ordennance ADD CONSTRAINT FK_363B6D2291EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65CAB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65C3CD0AB46 FOREIGN KEY (ordennance_id) REFERENCES ordennance (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF63C105691 FOREIGN KEY (coach_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE category ADD nb_product INT DEFAULT NULL, ADD slug VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD ordenance_id INT DEFAULT NULL, ADD note VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD89133B9 FOREIGN KEY (ordenance_id) REFERENCES ordennance (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65E8AA0AD89133B9 ON rendez_vous (ordenance_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD89133B9');
        $this->addSql('ALTER TABLE ordennance DROP FOREIGN KEY FK_363B6D2291EF7EAA');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65CAB0D61F7');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65C3CD0AB46');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6E3797A94');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF681C06096');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF63C105691');
        $this->addSql('DROP TABLE medicament');
        $this->addSql('DROP TABLE ordennance');
        $this->addSql('DROP TABLE ordennance_ligne');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE seance');
        $this->addSql('ALTER TABLE category DROP nb_product, DROP slug');
        $this->addSql('DROP INDEX UNIQ_65E8AA0AD89133B9 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP ordenance_id, DROP note');
    }
}
