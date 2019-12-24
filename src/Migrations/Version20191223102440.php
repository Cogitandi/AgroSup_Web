<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223102440 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, year_plan_id INT NOT NULL, name VARCHAR(30) NOT NULL, INDEX IDX_5BF54558FA36FFE2 (year_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcel (id INT AUTO_INCREMENT NOT NULL, arimr_operator_id INT DEFAULT NULL, year_plan_id INT NOT NULL, field_id INT DEFAULT NULL, parcel_number VARCHAR(10) NOT NULL, cultivated_area INT NOT NULL, fuel_application TINYINT(1) NOT NULL, INDEX IDX_C99B5D60CD847389 (arimr_operator_id), INDEX IDX_C99B5D60FA36FFE2 (year_plan_id), INDEX IDX_C99B5D60443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60CD847389 FOREIGN KEY (arimr_operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60443707B0');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE parcel');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
