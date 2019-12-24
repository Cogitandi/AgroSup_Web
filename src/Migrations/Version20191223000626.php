<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223000626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(30) NOT NULL, surname VARCHAR(30) NOT NULL, arimr_number INT NOT NULL, disable TINYINT(1) NOT NULL, INDEX IDX_D7A6A781A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE year_plan (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, start_year INT NOT NULL, end_year INT NOT NULL, is_closed TINYINT(1) NOT NULL, INDEX IDX_F74F81FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE year_plan ADD CONSTRAINT FK_F74F81FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator DROP FOREIGN KEY FK_D7A6A781A76ED395');
        $this->addSql('ALTER TABLE year_plan DROP FOREIGN KEY FK_F74F81FA76ED395');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE year_plan');
    }
}
