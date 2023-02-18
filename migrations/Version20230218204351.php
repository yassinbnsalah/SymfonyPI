<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218204351 extends AbstractMigration
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
        $this->addSql('ALTER TABLE ordennance ADD CONSTRAINT FK_363B6D2291EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65CAB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65C3CD0AB46 FOREIGN KEY (ordennance_id) REFERENCES ordennance (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordennance DROP FOREIGN KEY FK_363B6D2291EF7EAA');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65CAB0D61F7');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65C3CD0AB46');
        $this->addSql('DROP TABLE medicament');
        $this->addSql('DROP TABLE ordennance');
        $this->addSql('DROP TABLE ordennance_ligne');
    }
}
