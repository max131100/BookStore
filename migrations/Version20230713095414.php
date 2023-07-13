<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713095414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_to_book_category (book_id INT NOT NULL, book_category_id INT NOT NULL, INDEX IDX_57511BE216A2B381 (book_id), INDEX IDX_57511BE240B1D29E (book_category_id), PRIMARY KEY(book_id, book_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_format (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_to_book_format (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, format_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_D02DE22216A2B381 (book_id), INDEX IDX_D02DE222D629F605 (format_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, rating INT NOT NULL, content LONGTEXT NOT NULL, author VARCHAR(255) NOT NULL, created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_794381C616A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_to_book_category ADD CONSTRAINT FK_57511BE216A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_to_book_category ADD CONSTRAINT FK_57511BE240B1D29E FOREIGN KEY (book_category_id) REFERENCES book_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_to_book_format ADD CONSTRAINT FK_D02DE22216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_to_book_format ADD CONSTRAINT FK_D02DE222D629F605 FOREIGN KEY (format_id) REFERENCES book_format (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_book_category DROP FOREIGN KEY FK_7A5A379416A2B381');
        $this->addSql('ALTER TABLE book_book_category DROP FOREIGN KEY FK_7A5A379440B1D29E');
        $this->addSql('DROP TABLE book_book_category');
        $this->addSql('ALTER TABLE book ADD isbn VARCHAR(13) NOT NULL, ADD description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_book_category (book_id INT NOT NULL, book_category_id INT NOT NULL, INDEX IDX_7A5A379416A2B381 (book_id), INDEX IDX_7A5A379440B1D29E (book_category_id), PRIMARY KEY(book_id, book_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE book_book_category ADD CONSTRAINT FK_7A5A379416A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_book_category ADD CONSTRAINT FK_7A5A379440B1D29E FOREIGN KEY (book_category_id) REFERENCES book_category (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_to_book_category DROP FOREIGN KEY FK_57511BE216A2B381');
        $this->addSql('ALTER TABLE book_to_book_category DROP FOREIGN KEY FK_57511BE240B1D29E');
        $this->addSql('ALTER TABLE book_to_book_format DROP FOREIGN KEY FK_D02DE22216A2B381');
        $this->addSql('ALTER TABLE book_to_book_format DROP FOREIGN KEY FK_D02DE222D629F605');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C616A2B381');
        $this->addSql('DROP TABLE book_to_book_category');
        $this->addSql('DROP TABLE book_format');
        $this->addSql('DROP TABLE book_to_book_format');
        $this->addSql('DROP TABLE review');
        $this->addSql('ALTER TABLE book DROP isbn, DROP description');
    }
}
