<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250531141802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C8F5EA509');
        $this->addSql('ALTER TABLE type DROP FOREIGN KEY FK_8CDE57297ECF78B0');
        $this->addSql('ALTER TABLE type_professeur DROP FOREIGN KEY FK_AA684BCBAB22EE9');
        $this->addSql('ALTER TABLE type_professeur DROP FOREIGN KEY FK_AA684BCC54C8C93');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C17ECF78B0');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1BAB22EE9');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE type_professeur');
        $this->addSql('DROP TABLE emploi_du_temps');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, horaire VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, salle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_FDCA8C9C8F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, matiere VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_8CDE57297ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_professeur (type_id INT NOT NULL, professeur_id INT NOT NULL, INDEX IDX_AA684BCC54C8C93 (type_id), INDEX IDX_AA684BCBAB22EE9 (professeur_id), PRIMARY KEY(type_id, professeur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE emploi_du_temps (id INT AUTO_INCREMENT NOT NULL, professeur_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_F86B32C1BAB22EE9 (professeur_id), INDEX IDX_F86B32C17ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE57297ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE type_professeur ADD CONSTRAINT FK_AA684BCBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_professeur ADD CONSTRAINT FK_AA684BCC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C17ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
