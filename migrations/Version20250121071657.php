<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121071657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, programme_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_8F87BF9662BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, horaire VARCHAR(255) NOT NULL, salle VARCHAR(255) NOT NULL, INDEX IDX_FDCA8C9C8F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professeur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nb_heure_max INT NOT NULL, preference_horaire VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programme (id INT AUTO_INCREMENT NOT NULL, matiere VARCHAR(255) NOT NULL, heures VARCHAR(255) NOT NULL, niveau VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, matiere VARCHAR(255) NOT NULL, INDEX IDX_8CDE57297ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_professeur (type_id INT NOT NULL, professeur_id INT NOT NULL, INDEX IDX_AA684BCC54C8C93 (type_id), INDEX IDX_AA684BCBAB22EE9 (professeur_id), PRIMARY KEY(type_id, professeur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, professeur_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, INDEX IDX_8D93D649BAB22EE9 (professeur_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9662BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE57297ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE type_professeur ADD CONSTRAINT FK_AA684BCC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_professeur ADD CONSTRAINT FK_AA684BCBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9662BB7AEE');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C8F5EA509');
        $this->addSql('ALTER TABLE type DROP FOREIGN KEY FK_8CDE57297ECF78B0');
        $this->addSql('ALTER TABLE type_professeur DROP FOREIGN KEY FK_AA684BCC54C8C93');
        $this->addSql('ALTER TABLE type_professeur DROP FOREIGN KEY FK_AA684BCBAB22EE9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BAB22EE9');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('DROP TABLE programme');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE type_professeur');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
