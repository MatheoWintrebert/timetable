<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405153805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFF46CD258');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B3E9C81');
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFB3E9C81');
        $this->addSql('ALTER TABLE professeur_matiere DROP FOREIGN KEY FK_FBC82ABCBAB22EE9');
        $this->addSql('ALTER TABLE professeur_matiere DROP FOREIGN KEY FK_FBC82ABCF46CD258');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE professeur_matiere');
        $this->addSql('DROP INDEX IDX_8F87BF96B3E9C81 ON classe');
        $this->addSql('ALTER TABLE classe ADD programme_id INT DEFAULT NULL, DROP niveau_id');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9662BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF9662BB7AEE ON classe (programme_id)');
        $this->addSql('ALTER TABLE professeur CHANGE preference_horaire preference_horaire JSON DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_3DDCB9FFB3E9C81 ON programme');
        $this->addSql('DROP INDEX IDX_3DDCB9FFF46CD258 ON programme');
        $this->addSql('ALTER TABLE programme ADD matiere VARCHAR(255) NOT NULL, ADD heures VARCHAR(255) NOT NULL, ADD niveau VARCHAR(255) NOT NULL, DROP niveau_id, DROP matiere_id, DROP nb_heures');
    }
}
