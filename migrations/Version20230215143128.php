<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215143128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disponibility (id INT AUTO_INCREMENT NOT NULL, doctor_id INT DEFAULT NULL, date_dispo DATE NOT NULL, heure_start TIME NOT NULL, heure_end TIME NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_38BB926087F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE disponibility ADD CONSTRAINT FK_38BB926087F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE subscription ADD state VARCHAR(125) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE image image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibility DROP FOREIGN KEY FK_38BB926087F4FB17');
        $this->addSql('DROP TABLE disponibility');
        $this->addSql('ALTER TABLE subscription DROP state');
        $this->addSql('ALTER TABLE `user` CHANGE image image VARCHAR(255) DEFAULT NULL');
    }
}
