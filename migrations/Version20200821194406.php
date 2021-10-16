<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200821194406 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blogposts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, tags VARCHAR(255) NOT NULL, lang VARCHAR(255) NOT NULL, sticky VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE links (id INT AUTO_INCREMENT NOT NULL, pozycja VARCHAR(255) NOT NULL, etykieta VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, strona VARCHAR(255) NOT NULL, lang VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loginlogs (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, czas VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, ranga VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, link VARCHAR(255) NOT NULL, etykieta VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, lang VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blogposts');
        $this->addSql('DROP TABLE links');
        $this->addSql('DROP TABLE loginlogs');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE users');
    }
}
