<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107085153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, company_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A105B0D8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developer_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, experience_level INT NOT NULL, min_salary INT DEFAULT NULL, bio LONGTEXT DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_EFC94CA4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developer_profile_language (developer_profile_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_83F643986444A7E (developer_profile_id), INDEX IDX_83F6439882F1BAF4 (language_id), PRIMARY KEY(developer_profile_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, developer_profile_id INT NOT NULL, rating INT NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_1323A5756444A7E (developer_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, company_profile_id INT NOT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, experience_required INT DEFAULT NULL, salary INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_288A3A4E7174FB2E (company_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer_technology (job_offer_id INT NOT NULL, technology_id INT NOT NULL, INDEX IDX_B52F711F3481D195 (job_offer_id), INDEX IDX_B52F711F4235D463 (technology_id), PRIMARY KEY(job_offer_id, technology_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matching (id INT AUTO_INCREMENT NOT NULL, developer_profile_id INT NOT NULL, job_offer_id INT NOT NULL, score DOUBLE PRECISION DEFAULT NULL, INDEX IDX_DC10F2896444A7E (developer_profile_id), INDEX IDX_DC10F2893481D195 (job_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statistics (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, views INT NOT NULL, INDEX IDX_E2D38B22A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE technology (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_profile ADD CONSTRAINT FK_A105B0D8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE developer_profile ADD CONSTRAINT FK_EFC94CA4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE developer_profile_language ADD CONSTRAINT FK_83F643986444A7E FOREIGN KEY (developer_profile_id) REFERENCES developer_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_profile_language ADD CONSTRAINT FK_83F6439882F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5756444A7E FOREIGN KEY (developer_profile_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4E7174FB2E FOREIGN KEY (company_profile_id) REFERENCES company_profile (id)');
        $this->addSql('ALTER TABLE job_offer_technology ADD CONSTRAINT FK_B52F711F3481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_technology ADD CONSTRAINT FK_B52F711F4235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matching ADD CONSTRAINT FK_DC10F2896444A7E FOREIGN KEY (developer_profile_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE matching ADD CONSTRAINT FK_DC10F2893481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
        $this->addSql('ALTER TABLE statistics ADD CONSTRAINT FK_E2D38B22A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile DROP FOREIGN KEY FK_A105B0D8A76ED395');
        $this->addSql('ALTER TABLE developer_profile DROP FOREIGN KEY FK_EFC94CA4A76ED395');
        $this->addSql('ALTER TABLE developer_profile_language DROP FOREIGN KEY FK_83F643986444A7E');
        $this->addSql('ALTER TABLE developer_profile_language DROP FOREIGN KEY FK_83F6439882F1BAF4');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5756444A7E');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4E7174FB2E');
        $this->addSql('ALTER TABLE job_offer_technology DROP FOREIGN KEY FK_B52F711F3481D195');
        $this->addSql('ALTER TABLE job_offer_technology DROP FOREIGN KEY FK_B52F711F4235D463');
        $this->addSql('ALTER TABLE matching DROP FOREIGN KEY FK_DC10F2896444A7E');
        $this->addSql('ALTER TABLE matching DROP FOREIGN KEY FK_DC10F2893481D195');
        $this->addSql('ALTER TABLE statistics DROP FOREIGN KEY FK_E2D38B22A76ED395');
        $this->addSql('DROP TABLE company_profile');
        $this->addSql('DROP TABLE developer_profile');
        $this->addSql('DROP TABLE developer_profile_language');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('DROP TABLE job_offer_technology');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE matching');
        $this->addSql('DROP TABLE statistics');
        $this->addSql('DROP TABLE technology');
    }
}
