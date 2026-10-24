#! /bin/bash
#create datafixtures

rm -rf public/upload/* var/cache var/log

php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load --append