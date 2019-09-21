<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190921115735 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, userid INT NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(10) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', INDEX IDX_AA5A118EF132696E (userid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (issn VARCHAR(13) NOT NULL, customerid VARCHAR(180) NOT NULL, authorid INT NOT NULL, name VARCHAR(255) NOT NULL, status ENUM(\'new\', \'pending\', \'in_review\', \'approved\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_extended)\', createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, INDEX IDX_B3BA5A5A64FBF307 (customerid), INDEX IDX_B3BA5A5A3412DD5F (authorid), PRIMARY KEY(issn)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (uuid VARCHAR(180) NOT NULL, authorid INT NOT NULL, firstName VARCHAR(32) NOT NULL, lastName VARCHAR(32) NOT NULL, dateOfBirth DATE NOT NULL, status ENUM(\'new\', \'pending\', \'in_review\', \'approved\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_extended)\', createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deleteAt DATETIME DEFAULT NULL, INDEX IDX_62534E213412DD5F (authorid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, authorid INT NOT NULL, request VARCHAR(255) NOT NULL, response VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', INDEX IDX_F08FC65C3412DD5F (authorid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, authorid INT DEFAULT NULL, userid INT NOT NULL, productid VARCHAR(13) DEFAULT NULL, type VARCHAR(10) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', INDEX IDX_6000B0D33412DD5F (authorid), INDEX IDX_6000B0D3F132696E (userid), INDEX IDX_6000B0D3A3FDB2A7 (productid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EF132696E FOREIGN KEY (userid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A64FBF307 FOREIGN KEY (customerid) REFERENCES customers (uuid)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A3412DD5F FOREIGN KEY (authorid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E213412DD5F FOREIGN KEY (authorid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65C3412DD5F FOREIGN KEY (authorid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D33412DD5F FOREIGN KEY (authorid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3F132696E FOREIGN KEY (userid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A3FDB2A7 FOREIGN KEY (productid) REFERENCES products (issn)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A3FDB2A7');
        $this->addSql('ALTER TABLE tokens DROP FOREIGN KEY FK_AA5A118EF132696E');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A3412DD5F');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E213412DD5F');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65C3412DD5F');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D33412DD5F');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3F132696E');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A64FBF307');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE notifications');
    }
}
