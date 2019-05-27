# keyring

web application based on Symfony framework, to mange keys, magnetic cards... on our labs

## Authors
[Hugo BLACHERE](https://github.com/yugohug0)

[Maximilien GUERRERRO](https://github.com/GsxLephoque)

[Olivier CHABROL](https://github.com/olivierChabrol)

## Installation
In the keyring directory, create a *.env* file 

*.env* file will contains your credentials to connect the database:
```
# This file is a "template" of which env vars need to be defined for your application
# Copy this file to .env file for development, create environment variables when deploying to production
# https://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=0498cbd96ebd391bec0eb196a756bda1
DATABASE_URL="mysql://[dbuserName]:[dbPassword]@[ipAddress]:[port]/[dbName]"
# example : DATABASE_URL="mysql://doe:pass@localhost:3306/keyringDb"

#TRUSTED_PROXIES=127.0.0.1,127.0.0.2
#TRUSTED_HOSTS=localhost,example.com
###< symfony/framework-bundle ###
```

Install composer : see https://getcomposer.org/download/ or
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```


use
```composer update```

Defaulf DB :
```
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `param` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `param` (`id`, `type`, `value`) VALUES
(1, 1, 'Carte magnétique'),
(2, 1, 'Clef'),
(3, 2, 'Site 1'),
(4, 2, 'Site 2'),
(5, 1, 'Carte Hertzienne'),
(6, 2, 'Site 3'),
(7, 3, 'Actif'),
(8, 3, 'Perdu'),
(9, 3, 'Volé'),
(10, 3, 'H.S.');

CREATE TABLE `pret` (
  `id` int(11) NOT NULL,
  `trousseau_id` int(11) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `description` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pret` (`id`, `trousseau_id`, `start`, `end`, `description`, `user_id`) VALUES
(8, 64, '2021-02-25 07:16:34', '2019-05-31 07:16:34', 'Lend description', 16);

CREATE TABLE `trousseau` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `site` int(11) NOT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modele` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_id` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `date_state` datetime DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `access` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_in` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_out` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trousseau` (`id`, `type`, `site`, `ref`, `modele`, `creator_id`, `state`, `date_state`, `creation_date`, `access`, `ticket_in`, `ticket_out`) VALUES
(64, 2, 3, 'Référence', 'Modèle', 16, 7, NULL, '2019-05-27 07:13:16', 'Accés', '', '');

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json_array)',
  `origine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `financement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `email`, `username`, `password`, `name`, `first_name`, `note`, `roles`, `origine`, `financement`, `equipe`) VALUES
(16, 'admin@domain.com', 'admin', '$2y$13$Umx6A8eN.7BSu4tnXMfnr.Zt9MOPGd73v9IKzkqdxqjNLQa6UXy26', 'admin', 'admin', NULL, '[\"ROLE_ADMIN\"]', '', NULL, NULL);

ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

ALTER TABLE `param`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pret`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_52ECE979A6DA9EEC` (`trousseau_id`),
  ADD KEY `IDX_52ECE979A76ED395` (`user_id`);

ALTER TABLE `trousseau`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_564FF31761220EA6` (`creator_id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `param`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `pret`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `trousseau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

ALTER TABLE `pret`
  ADD CONSTRAINT `FK_52ECE979A6DA9EEC` FOREIGN KEY (`trousseau_id`) REFERENCES `trousseau` (`id`),
  ADD CONSTRAINT `FK_52ECE979A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `trousseau`
  ADD CONSTRAINT `FK_564FF31761220EA6` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`);
COMMIT;
```