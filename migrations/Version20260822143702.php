<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260822143702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dossier_adhesion (id INT AUTO_INCREMENT NOT NULL, produit JSON NOT NULL, contact_urgence VARCHAR(255) NOT NULL, telephone_contact_urgence VARCHAR(255) NOT NULL, need_medical_certificate TINYINT NOT NULL, medical_certificate_file VARCHAR(255) DEFAULT NULL, ancien_adherent TINYINT NOT NULL, pass_port_code VARCHAR(255) DEFAULT NULL, autorisation_parentale TINYINT DEFAULT NULL, droit_aimage TINYINT NOT NULL, cgv TINYINT NOT NULL, reglement_interieur TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user ADD dossier_adhesion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BDAB97D4 FOREIGN KEY (dossier_adhesion_id) REFERENCES dossier_adhesion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BDAB97D4 ON user (dossier_adhesion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dossier_adhesion');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BDAB97D4');
        $this->addSql('DROP INDEX UNIQ_8D93D649BDAB97D4 ON user');
        $this->addSql('ALTER TABLE user DROP dossier_adhesion_id');
    }
}
