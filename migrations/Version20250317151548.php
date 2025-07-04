<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317151548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE professeur_matiere (professeur_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_FBC82ABCBAB22EE9 (professeur_id), INDEX IDX_FBC82ABCF46CD258 (matiere_id), PRIMARY KEY(professeur_id, matiere_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE professeur_matiere ADD CONSTRAINT FK_FBC82ABCBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeur_matiere ADD CONSTRAINT FK_FBC82ABCF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeur CHANGE preference_horaire preference_horaire JSON DEFAULT NULL, CHANGE nb_heure_max nb_heures_max INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE professeur_matiere DROP FOREIGN KEY FK_FBC82ABCBAB22EE9');
        $this->addSql('ALTER TABLE professeur_matiere DROP FOREIGN KEY FK_FBC82ABCF46CD258');
        $this->addSql('DROP TABLE professeur_matiere');
        $this->addSql('ALTER TABLE professeur CHANGE preference_horaire preference_horaire VARCHAR(255) NOT NULL, CHANGE nb_heures_max nb_heure_max INT NOT NULL');
    }
}
