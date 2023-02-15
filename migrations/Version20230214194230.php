<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214194230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous ADD fromuser_id INT DEFAULT NULL, ADD todoctor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AD36C4FC6 FOREIGN KEY (fromuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A646D20D9 FOREIGN KEY (todoctor_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0AD36C4FC6 ON rendez_vous (fromuser_id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0A646D20D9 ON rendez_vous (todoctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AD36C4FC6');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A646D20D9');
        $this->addSql('DROP INDEX IDX_65E8AA0AD36C4FC6 ON rendez_vous');
        $this->addSql('DROP INDEX IDX_65E8AA0A646D20D9 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP fromuser_id, DROP todoctor_id');
    }
}
