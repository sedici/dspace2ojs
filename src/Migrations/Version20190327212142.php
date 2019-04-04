<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190327212142 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE setting_file (id INT AUTO_INCREMENT NOT NULL, parent_file VARCHAR(255) NOT NULL, into_section VARCHAR(255) NOT NULL, authors_group VARCHAR(255) NOT NULL, limit_files INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD setting_file_id INT NOT NULL, DROP parent_file, CHANGE date_created date_created VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CB99C984 FOREIGN KEY (setting_file_id) REFERENCES setting_file (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610CB99C984 ON file (setting_file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CB99C984');
        $this->addSql('DROP TABLE setting_file');
        $this->addSql('DROP INDEX IDX_8C9F3610CB99C984 ON file');
        $this->addSql('ALTER TABLE file ADD parent_file VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP setting_file_id, CHANGE date_created date_created DATE NOT NULL');
    }
}
