<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109234350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5756444A7E');
        $this->addSql('DROP INDEX IDX_1323A5756444A7E ON evaluation');
        $this->addSql('ALTER TABLE evaluation ADD evaluated_developer_id INT NOT NULL, ADD created_at DATETIME NOT NULL, DROP comment, CHANGE developer_profile_id evaluator_id INT NOT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57543575BE2 FOREIGN KEY (evaluator_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575B91B124D FOREIGN KEY (evaluated_developer_id) REFERENCES developer_profile (id)');
        $this->addSql('CREATE INDEX IDX_1323A57543575BE2 ON evaluation (evaluator_id)');
        $this->addSql('CREATE INDEX IDX_1323A575B91B124D ON evaluation (evaluated_developer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A57543575BE2');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575B91B124D');
        $this->addSql('DROP INDEX IDX_1323A57543575BE2 ON evaluation');
        $this->addSql('DROP INDEX IDX_1323A575B91B124D ON evaluation');
        $this->addSql('ALTER TABLE evaluation ADD developer_profile_id INT NOT NULL, ADD comment LONGTEXT DEFAULT NULL, DROP evaluator_id, DROP evaluated_developer_id, DROP created_at');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5756444A7E FOREIGN KEY (developer_profile_id) REFERENCES developer_profile (id)');
        $this->addSql('CREATE INDEX IDX_1323A5756444A7E ON evaluation (developer_profile_id)');
    }
}
