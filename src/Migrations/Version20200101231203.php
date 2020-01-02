<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200101231203 extends AbstractMigration
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
        $this->addSql('ALTER TABLE operator CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT NULL, CHANGE disable disable TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name name VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE choosed_year_plan_id choosed_year_plan_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE year_plan CHANGE is_closed is_closed TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE field CHANGE plant_id plant_id INT DEFAULT NULL, CHANGE plant_variety plant_variety VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE operator CHANGE arimr_number arimr_number VARCHAR(11) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE disable disable TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE parcel CHANGE arimr_operator_id arimr_operator_id INT DEFAULT NULL, CHANGE field_id field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name name VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE choosed_year_plan_id choosed_year_plan_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE year_plan CHANGE is_closed is_closed TINYINT(1) DEFAULT \'NULL\'');
    }
}
