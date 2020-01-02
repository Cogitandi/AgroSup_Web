<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200101223723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE field CHANGE plant_id plant_id INT DEFAULT NULL, CHANGE plant_variety plant_variety VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE operator CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name name VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD choosed_year_plan_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F4F98E78 FOREIGN KEY (choosed_year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F4F98E78 ON user (choosed_year_plan_id)');
        $this->addSql('ALTER TABLE year_plan CHANGE is_closed is_closed TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE field CHANGE plant_id plant_id INT DEFAULT NULL, CHANGE plant_variety plant_variety VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE operator CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name name VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F4F98E78');
        $this->addSql('DROP INDEX IDX_8D93D649F4F98E78 ON user');
        $this->addSql('ALTER TABLE user DROP choosed_year_plan_id, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE year_plan CHANGE is_closed is_closed TINYINT(1) DEFAULT \'NULL\'');
    }
}
