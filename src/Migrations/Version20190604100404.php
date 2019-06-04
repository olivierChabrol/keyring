<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190604100404 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD host_id INT DEFAULT NULL, ADD position INT DEFAULT NULL, ADD nationality VARCHAR(5) DEFAULT NULL, ADD arrival DATETIME DEFAULT NULL, ADD departure DATETIME DEFAULT NULL, CHANGE equipe equipe INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491FB8D185 ON user (host_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491FB8D185');
        $this->addSql('DROP INDEX IDX_8D93D6491FB8D185 ON user');
        $this->addSql('ALTER TABLE user DROP host_id, DROP position, DROP nationality, DROP arrival, DROP departure, CHANGE equipe equipe INT DEFAULT NULL');
    }
}
