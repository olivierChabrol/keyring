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
DATABASE_URL="mysql://[dbuserName]:[dbPassword]!@[ipAddress]:[port]/[dbName]"
# example : DATABASE_URL="mysql://doe:pass!@localhost:3306/keyringDb"

#TRUSTED_PROXIES=127.0.0.1,127.0.0.2
#TRUSTED_HOSTS=localhost,example.com
###< symfony/framework-bundle ###
```
