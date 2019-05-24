<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524072450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE note note VARCHAR(255) DEFAULT NULL, CHANGE financement financement VARCHAR(255) DEFAULT NULL, CHANGE equipe equipe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE trousseau CHANGE date_state date_state DATETIME DEFAULT NULL, CHANGE access access VARCHAR(255) DEFAULT NULL, CHANGE ticket_in ticket_in VARCHAR(50) DEFAULT NULL, CHANGE ticket_out ticket_out VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE pret CHANGE trousseau_id trousseau_id INT DEFAULT NULL, CHANGE end end DATETIME DEFAULT NULL, CHANGE description description VARCHAR(1500) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pret CHANGE trousseau_id trousseau_id INT DEFAULT NULL, CHANGE end end DATETIME DEFAULT \'NULL\', CHANGE description description VARCHAR(1500) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE trousseau CHANGE date_state date_state DATETIME DEFAULT \'NULL\', CHANGE access access VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ticket_in ticket_in VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ticket_out ticket_out VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE note note VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE financement financement VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE equipe equipe VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
