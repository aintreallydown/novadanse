<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260827103024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classes_registration (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, date_of_birth DATE DEFAULT NULL, created_at DATE DEFAULT NULL, produit JSON NOT NULL, contact_urgence VARCHAR(255) NOT NULL, telephone_contact_urgence VARCHAR(255) NOT NULL, need_medical_certificate TINYINT NOT NULL, medical_certificate_file VARCHAR(255) DEFAULT NULL, ancien_adherent TINYINT NOT NULL, pass_port_code VARCHAR(255) DEFAULT NULL, autorisation_parentale TINYINT DEFAULT NULL, droit_image TINYINT NOT NULL, cgv TINYINT NOT NULL, reglement_interieur TINYINT NOT NULL, promo_estres_saint_denis TINYINT DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_1D14AED6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, uid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, address VARCHAR(255) NOT NULL, promo_multiple_cours VARCHAR(255) DEFAULT NULL, price_to_pay VARCHAR(255) DEFAULT NULL, classes_registration_id JSON DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649539B0606 (uid), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE classes_registration ADD CONSTRAINT FK_1D14AED6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classes_registration DROP FOREIGN KEY FK_1D14AED6A76ED395');
        $this->addSql('DROP TABLE classes_registration');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
