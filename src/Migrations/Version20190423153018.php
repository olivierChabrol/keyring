<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190423153018 extends AbstractMigration
{

  
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE param (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `pret` (`id` int(11) NOT NULL, `trousseau_id` int(11) DEFAULT NULL, `start` datetime NOT NULL, `end` datetime DEFAULT NULL, `description` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `user_id` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->addSql('CREATE TABLE `trousseau` (`id` int(11) NOT NULL, `type` int(11) NOT NULL, `site` int(11) NOT NULL, `ref` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `modele` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `creator_id` int(11) NOT NULL, `state` int(11) NOT NULL, `date_state` datetime DEFAULT NULL, `creation_date` datetime NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->addSql('CREATE TABLE `user` (`id` int(11) NOT NULL, `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `roles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'["ROLE_USER"]\', `origine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `pret` ADD UNIQUE KEY `UNIQ_52ECE979A6DA9EEC` (`trousseau_id`), ADD KEY `IDX_52ECE979A76ED395` (`user_id`)');

        $this->addSql('ALTER TABLE `trousseau` ADD KEY `IDX_564FF31761220EA6` (`creator_id`);');
        $this->addSql('ALTER TABLE `pret` ADD CONSTRAINT `FK_52ECE979A6DA9EEC` FOREIGN KEY (`trousseau_id`) REFERENCES `trousseau` (`id`)');
        $this->addSql('ALTER TABLE `pret` ADD CONSTRAINT `FK_52ECE979A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)');
        $this->addSql('ALTER TABLE `trousseau` ADD CONSTRAINT `FK_564FF31761220EA6` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`)');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE param');
        $this->addSql('DROP TABLE pret');
        $this->addSql('DROP TABLE trousseau');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE pret');
    }
}
