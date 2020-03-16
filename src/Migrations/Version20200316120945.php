<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316120945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, year_plan_id INT NOT NULL, plant_id INT DEFAULT NULL, name VARCHAR(30) NOT NULL, plant_variety VARCHAR(20) DEFAULT NULL, INDEX IDX_5BF54558FA36FFE2 (year_plan_id), INDEX IDX_5BF545581D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, year_plan_id INT NOT NULL, first_name VARCHAR(30) NOT NULL, surname VARCHAR(30) NOT NULL, arimr_number VARCHAR(11) DEFAULT NULL, disable TINYINT(1) DEFAULT NULL, INDEX IDX_D7A6A781FA36FFE2 (year_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcel (id INT AUTO_INCREMENT NOT NULL, arimr_operator_id INT DEFAULT NULL, year_plan_id INT NOT NULL, field_id INT DEFAULT NULL, parcel_number VARCHAR(10) NOT NULL, cultivated_area INT NOT NULL, fuel_application TINYINT(1) NOT NULL, INDEX IDX_C99B5D60CD847389 (arimr_operator_id), INDEX IDX_C99B5D60FA36FFE2 (year_plan_id), INDEX IDX_C99B5D60443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, efa_nitrogen TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, choosed_year_plan_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F4F98E78 (choosed_year_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_plant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_49C1F62AA76ED395 (user_id), INDEX IDX_49C1F62A1D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE year_plan (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, start_year INT NOT NULL, end_year INT NOT NULL, is_closed TINYINT(1) DEFAULT NULL, INDEX IDX_F74F81FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545581D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60CD847389 FOREIGN KEY (arimr_operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60FA36FFE2 FOREIGN KEY (year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F4F98E78 FOREIGN KEY (choosed_year_plan_id) REFERENCES year_plan (id)');
        $this->addSql('ALTER TABLE user_plant ADD CONSTRAINT FK_49C1F62AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_plant ADD CONSTRAINT FK_49C1F62A1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE year_plan ADD CONSTRAINT FK_F74F81FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60443707B0');
        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60CD847389');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545581D935652');
        $this->addSql('ALTER TABLE user_plant DROP FOREIGN KEY FK_49C1F62A1D935652');
        $this->addSql('ALTER TABLE user_plant DROP FOREIGN KEY FK_49C1F62AA76ED395');
        $this->addSql('ALTER TABLE year_plan DROP FOREIGN KEY FK_F74F81FA76ED395');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558FA36FFE2');
        $this->addSql('ALTER TABLE operator DROP FOREIGN KEY FK_D7A6A781FA36FFE2');
        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60FA36FFE2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F4F98E78');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP TABLE parcel');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_plant');
        $this->addSql('DROP TABLE year_plan');
    }
}
