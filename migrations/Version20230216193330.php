<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216193330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, fromuser_id INT DEFAULT NULL, todoctor_id INT DEFAULT NULL, date_rv DATE NOT NULL, hour_rv TIME NOT NULL, date_passage_rv DATE NOT NULL, hour_passage_rv TIME NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_65E8AA0AD36C4FC6 (fromuser_id), INDEX IDX_65E8AA0A646D20D9 (todoctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD36C4FC6 FOREIGN KEY (fromuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A646D20D9 FOREIGN KEY (todoctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user CHANGE image image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD36C4FC6');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A646D20D9');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('ALTER TABLE `user` CHANGE image image VARCHAR(255) NOT NULL');
    }
}
