<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191230183157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator ADD year_plan_id INT NOT NULL, CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('CREATE INDEX IDX_D7A6A781FA36FFE2 ON operator (year_plan_id)');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator DROP FOREIGN KEY FK_D7A6A781FA36FFE2');
        $this->addSql('DROP INDEX IDX_D7A6A781FA36FFE2 ON operator');
        $this->addSql('ALTER TABLE operator DROP year_plan_id, CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
