<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412100935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hobby ADD dog_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hobby ADD CONSTRAINT FK_3964F337634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id)');
        $this->addSql('CREATE INDEX IDX_3964F337634DFEB ON hobby (dog_id)');
        $this->addSql('ALTER TABLE picture ADD dog_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F89634DFEB ON picture (dog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hobby DROP FOREIGN KEY FK_3964F337634DFEB');
        $this->addSql('DROP INDEX IDX_3964F337634DFEB ON hobby');
        $this->addSql('ALTER TABLE hobby DROP dog_id');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89634DFEB');
        $this->addSql('DROP INDEX IDX_16DB4F89634DFEB ON picture');
        $this->addSql('ALTER TABLE picture DROP dog_id');
    }
}
