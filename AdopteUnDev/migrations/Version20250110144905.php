<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110144905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, developer_profile_id INT DEFAULT NULL, job_offer_id INT DEFAULT NULL, INDEX IDX_68C58ED9A76ED395 (user_id), INDEX IDX_68C58ED96444A7E (developer_profile_id), INDEX IDX_68C58ED93481D195 (job_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED96444A7E FOREIGN KEY (developer_profile_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED93481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A76ED395');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED96444A7E');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED93481D195');
        $this->addSql('DROP TABLE favorite');
    }
}
