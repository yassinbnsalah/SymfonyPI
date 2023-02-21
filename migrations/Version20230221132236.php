<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221132236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE description description VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE disponibility CHANGE date_dispo date_dispo DATE NOT NULL, CHANGE heure_start heure_start TIME NOT NULL, CHANGE heure_end heure_end TIME NOT NULL, CHANGE note note VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD ordenance_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD89133B9 FOREIGN KEY (ordenance_id) REFERENCES ordennance (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65E8AA0AD89133B9 ON rendez_vous (ordenance_id)');
        $this->addSql('ALTER TABLE subscription CHANGE date_sub date_sub DATETIME NOT NULL, CHANGE date_expire date_expire DATETIME NOT NULL, CHANGE type type VARCHAR(125) NOT NULL, CHANGE paiement_type paiement_type VARCHAR(125) NOT NULL, CHANGE amount amount INT NOT NULL, CHANGE state state VARCHAR(125) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE nom nom VARCHAR(50) DEFAULT NULL, CHANGE description description VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE disponibility CHANGE date_dispo date_dispo DATE DEFAULT NULL, CHANGE heure_start heure_start TIME DEFAULT NULL, CHANGE heure_end heure_end TIME DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD89133B9');
        $this->addSql('DROP INDEX UNIQ_65E8AA0AD89133B9 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP ordenance_id, CHANGE note note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE date_sub date_sub DATETIME DEFAULT NULL, CHANGE date_expire date_expire DATETIME DEFAULT NULL, CHANGE type type VARCHAR(125) DEFAULT NULL, CHANGE paiement_type paiement_type VARCHAR(125) DEFAULT NULL, CHANGE amount amount INT DEFAULT NULL, CHANGE state state VARCHAR(125) DEFAULT NULL');
    }
}
