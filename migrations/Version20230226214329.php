<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226214329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(200) NOT NULL, image VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nb_product INT DEFAULT NULL, slug VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disponibility (id INT AUTO_INCREMENT NOT NULL, doctor_id INT DEFAULT NULL, date_dispo DATE NOT NULL, heure_start TIME NOT NULL, heure_end TIME NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_38BB926087F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicament (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordennance (id INT AUTO_INCREMENT NOT NULL, rendez_vous_id INT DEFAULT NULL, dateordenance DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_363B6D2291EF7EAA (rendez_vous_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordennance_ligne (id INT AUTO_INCREMENT NOT NULL, medicament_id INT DEFAULT NULL, ordennance_id INT DEFAULT NULL, qunatite INT NOT NULL, INDEX IDX_F1C2C65CAB0D61F7 (medicament_id), INDEX IDX_F1C2C65C3CD0AB46 (ordennance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, reference VARCHAR(100) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, shippingadress VARCHAR(150) DEFAULT NULL, date_order DATETIME DEFAULT NULL, note VARCHAR(255) NOT NULL, paiementmethod VARCHAR(125) DEFAULT NULL, invoiced TINYINT(1) DEFAULT NULL, INDEX IDX_F529939819EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, related_order_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_9CE58EE14584665A (product_id), INDEX IDX_9CE58EE12B1C2395 (related_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, seance_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, diet_note VARCHAR(255) NOT NULL, salle VARCHAR(15) NOT NULL, date_planning DATE NOT NULL, INDEX IDX_D499BFF6E3797A94 (seance_id), INDEX IDX_D499BFF681C06096 (activity_id), INDEX IDX_D499BFF63C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, buyprice INT DEFAULT NULL, sellprice INT DEFAULT NULL, quantity INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_29A5EC2712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, fromuser_id INT DEFAULT NULL, todoctor_id INT DEFAULT NULL, ordenance_id INT DEFAULT NULL, date_rv DATE NOT NULL, hour_rv TIME NOT NULL, date_passage_rv DATE NOT NULL, hour_passage_rv TIME NOT NULL, state VARCHAR(255) NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_65E8AA0AD36C4FC6 (fromuser_id), INDEX IDX_65E8AA0A646D20D9 (todoctor_id), UNIQUE INDEX UNIQ_65E8AA0AD89133B9 (ordenance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, niveau VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date_sub DATETIME NOT NULL, date_expire DATETIME NOT NULL, type VARCHAR(125) NOT NULL, paiement_type VARCHAR(125) NOT NULL, amount INT NOT NULL, state VARCHAR(125) NOT NULL, INDEX IDX_A3C664D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, date_ticket DATETIME DEFAULT NULL, INDEX IDX_97A0ADA37E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, cin INT NOT NULL, name VARCHAR(50) NOT NULL, numero INT NOT NULL, age INT NOT NULL, email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', activation_token VARCHAR(50) DEFAULT NULL, reset_token VARCHAR(50) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE disponibility ADD CONSTRAINT FK_38BB926087F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ordennance ADD CONSTRAINT FK_363B6D2291EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65CAB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
        $this->addSql('ALTER TABLE ordennance_ligne ADD CONSTRAINT FK_F1C2C65C3CD0AB46 FOREIGN KEY (ordennance_id) REFERENCES ordennance (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE12B1C2395 FOREIGN KEY (related_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF63C105691 FOREIGN KEY (coach_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD36C4FC6 FOREIGN KEY (fromuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A646D20D9 FOREIGN KEY (todoctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD89133B9 FOREIGN KEY (ordenance_id) REFERENCES ordennance (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibility DROP FOREIGN KEY FK_38BB926087F4FB17');
        $this->addSql('ALTER TABLE ordennance DROP FOREIGN KEY FK_363B6D2291EF7EAA');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65CAB0D61F7');
        $this->addSql('ALTER TABLE ordennance_ligne DROP FOREIGN KEY FK_F1C2C65C3CD0AB46');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE12B1C2395');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6E3797A94');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF681C06096');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF63C105691');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD36C4FC6');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A646D20D9');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD89133B9');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA37E3C61F9');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE disponibility');
        $this->addSql('DROP TABLE medicament');
        $this->addSql('DROP TABLE ordennance');
        $this->addSql('DROP TABLE ordennance_ligne');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
