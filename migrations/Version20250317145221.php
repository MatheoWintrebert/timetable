<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317145221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE niveau (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9662BB7AEE');
        $this->addSql('DROP INDEX IDX_8F87BF9662BB7AEE ON classe');
        $this->addSql('ALTER TABLE classe ADD niveau_id INT NOT NULL, DROP programme_id, DROP niveau');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF96B3E9C81 ON classe (niveau_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B3E9C81');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP INDEX IDX_8F87BF96B3E9C81 ON classe');
        $this->addSql('ALTER TABLE classe ADD programme_id INT DEFAULT NULL, ADD niveau VARCHAR(255) NOT NULL, DROP niveau_id');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9662BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F87BF9662BB7AEE ON classe (programme_id)');
    }
}
