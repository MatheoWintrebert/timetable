<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410124321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emploi_du_temps (id INT AUTO_INCREMENT NOT NULL, professeur_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_F86B32C1BAB22EE9 (professeur_id), INDEX IDX_F86B32C17ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C17ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1BAB22EE9');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C17ECF78B0');
        $this->addSql('DROP TABLE emploi_du_temps');
    }
}
