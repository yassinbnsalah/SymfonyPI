<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230219191502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nb_product INT DEFAULT NULL, slug VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, seance_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, diet_note VARCHAR(255) NOT NULL, salle VARCHAR(15) NOT NULL, date_planning DATE NOT NULL, INDEX IDX_D499BFF6E3797A94 (seance_id), INDEX IDX_D499BFF681C06096 (activity_id), INDEX IDX_D499BFF63C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, buyprice INT DEFAULT NULL, sellprice INT DEFAULT NULL, quantity INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_29A5EC2712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, fromuser_id INT DEFAULT NULL, todoctor_id INT DEFAULT NULL, date_rv DATE NOT NULL, hour_rv TIME NOT NULL, date_passage_rv DATE NOT NULL, hour_passage_rv TIME NOT NULL, state VARCHAR(255) NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_65E8AA0AD36C4FC6 (fromuser_id), INDEX IDX_65E8AA0A646D20D9 (todoctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, niveau VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF63C105691 FOREIGN KEY (coach_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD36C4FC6 FOREIGN KEY (fromuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A646D20D9 FOREIGN KEY (todoctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user CHANGE image image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6E3797A94');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF681C06096');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF63C105691');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD36C4FC6');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A646D20D9');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('DROP TABLE seance');
        $this->addSql('ALTER TABLE `user` CHANGE image image VARCHAR(255) NOT NULL');
    }
}
