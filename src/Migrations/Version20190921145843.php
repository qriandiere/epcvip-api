<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190921145843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customers (uuid VARCHAR(180) NOT NULL, firstName VARCHAR(32) NOT NULL, lastName VARCHAR(32) NOT NULL, dateOfBirth DATE NOT NULL, status ENUM(\'new\', \'pending\', \'in_review\', \'approved\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_extended)\', createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deleteAt DATETIME DEFAULT NULL, authorId INT NOT NULL, INDEX IDX_62534E21A196F9FD (authorId), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', authorId INT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), INDEX IDX_1483A5E9A196F9FD (authorId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, request VARCHAR(255) NOT NULL, response VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', authorId INT NOT NULL, INDEX IDX_F08FC65CA196F9FD (authorId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(15) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', authorId INT NOT NULL, INDEX IDX_AA5A118EA196F9FD (authorId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (issn VARCHAR(13) NOT NULL, name VARCHAR(255) NOT NULL, status ENUM(\'new\', \'pending\', \'in_review\', \'approved\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_extended)\', createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, customerId VARCHAR(180) NOT NULL, authorId INT NOT NULL, INDEX IDX_B3BA5A5AF17FD7A5 (customerId), INDEX IDX_B3BA5A5AA196F9FD (authorId), PRIMARY KEY(issn)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, status ENUM(\'new\', \'active\', \'inactive\', \'deleted\') NOT NULL COMMENT \'(DC2Type:enum_status_default)\', authorId INT DEFAULT NULL, userId INT NOT NULL, productId VARCHAR(13) DEFAULT NULL, INDEX IDX_6000B0D3A196F9FD (authorId), INDEX IDX_6000B0D364B64DCC (userId), INDEX IDX_6000B0D336799605 (productId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21A196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CA196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EA196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF17FD7A5 FOREIGN KEY (customerId) REFERENCES customers (uuid)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A196F9FD FOREIGN KEY (authorId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D364B64DCC FOREIGN KEY (userId) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D336799605 FOREIGN KEY (productId) REFERENCES products (issn)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AF17FD7A5');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21A196F9FD');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A196F9FD');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CA196F9FD');
        $this->addSql('ALTER TABLE tokens DROP FOREIGN KEY FK_AA5A118EA196F9FD');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA196F9FD');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A196F9FD');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D364B64DCC');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D336799605');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE notifications');
    }
}
