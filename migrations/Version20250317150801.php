<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317150801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE programme ADD niveau_id INT NOT NULL, ADD matiere_id INT NOT NULL, ADD nb_heures INT NOT NULL, DROP matiere, DROP heures, DROP niveau');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FFB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FFF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('CREATE INDEX IDX_3DDCB9FFB3E9C81 ON programme (niveau_id)');
        $this->addSql('CREATE INDEX IDX_3DDCB9FFF46CD258 ON programme (matiere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFF46CD258');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFB3E9C81');
        $this->addSql('DROP INDEX IDX_3DDCB9FFB3E9C81 ON programme');
        $this->addSql('DROP INDEX IDX_3DDCB9FFF46CD258 ON programme');
        $this->addSql('ALTER TABLE programme ADD matiere VARCHAR(255) NOT NULL, ADD heures VARCHAR(255) NOT NULL, ADD niveau VARCHAR(255) NOT NULL, DROP niveau_id, DROP matiere_id, DROP nb_heures');
    }
}
