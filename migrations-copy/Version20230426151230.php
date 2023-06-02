<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426151230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member CHANGE lastname lastname VARCHAR(64) DEFAULT NULL, CHANGE firstname firstname VARCHAR(64) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(64) DEFAULT NULL, CHANGE city city VARCHAR(35) DEFAULT NULL, CHANGE slug slug VARCHAR(65) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member CHANGE lastname lastname VARCHAR(64) NOT NULL, CHANGE firstname firstname VARCHAR(64) NOT NULL, CHANGE postal_code postal_code VARCHAR(64) NOT NULL, CHANGE city city VARCHAR(35) NOT NULL, CHANGE slug slug VARCHAR(65) NOT NULL');
    }
}
