#! /bin/bash
# deploy

php bin/console c:w

php bin/console importmap:install
php bin/console sass:build
php bin/console asset-map:compile
php bin/console cache:clear

php bin/console doctrine:schema:update --force

