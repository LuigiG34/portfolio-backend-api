<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260321154103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, status VARCHAR(10) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE degree (id INT AUTO_INCREMENT NOT NULL, graduated_at DATE NOT NULL, title VARCHAR(255) NOT NULL, school_name VARCHAR(255) NOT NULL, image_id INT NOT NULL, UNIQUE INDEX UNIQ_A7A36D633DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, started_at DATE NOT NULL, ended_at DATE DEFAULT NULL, is_current TINYINT NOT NULL, job_title VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, image_id INT NOT NULL, UNIQUE INDEX UNIQ_2FB3D0EE3DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE project_technology (project_id INT NOT NULL, technology_id INT NOT NULL, INDEX IDX_ECC5297F166D1F9C (project_id), INDEX IDX_ECC5297F4235D463 (technology_id), PRIMARY KEY (project_id, technology_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, experience_id INT NOT NULL, INDEX IDX_527EDB2546E90E27 (experience_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE technology (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_id INT NOT NULL, UNIQUE INDEX UNIQ_F463524D5E237E06 (name), UNIQUE INDEX UNIQ_F463524D3DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE degree ADD CONSTRAINT FK_A7A36D633DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE project_technology ADD CONSTRAINT FK_ECC5297F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_technology ADD CONSTRAINT FK_ECC5297F4235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2546E90E27 FOREIGN KEY (experience_id) REFERENCES experience (id)');
        $this->addSql('ALTER TABLE technology ADD CONSTRAINT FK_F463524D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE degree DROP FOREIGN KEY FK_A7A36D633DA5256D');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE3DA5256D');
        $this->addSql('ALTER TABLE project_technology DROP FOREIGN KEY FK_ECC5297F166D1F9C');
        $this->addSql('ALTER TABLE project_technology DROP FOREIGN KEY FK_ECC5297F4235D463');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2546E90E27');
        $this->addSql('ALTER TABLE technology DROP FOREIGN KEY FK_F463524D3DA5256D');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE degree');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_technology');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE technology');
        $this->addSql('DROP TABLE user');
    }
}
