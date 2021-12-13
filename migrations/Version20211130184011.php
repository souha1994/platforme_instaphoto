<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211130184011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F61220EA6');
        $this->addSql('DROP INDEX IDX_C53D045F61220EA6 ON image');
        $this->addSql('ALTER TABLE image CHANGE creator_id creator INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FBC06EA63 FOREIGN KEY (creator) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C53D045FBC06EA63 ON image (creator)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FBC06EA63');
        $this->addSql('DROP INDEX IDX_C53D045FBC06EA63 ON image');
        $this->addSql('ALTER TABLE image CHANGE creator creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F61220EA6 ON image (creator_id)');
    }
}
