#! /bin/bash
# deploy

php bin/console c:w

rm -rf vendor assets/vendor
composer install --no-dev --optimize-autoloader

php bin/console importmap:install
php bin/console sass:build
php bin/console asset-map:compile
php bin/console cache:clear

php bin/console doctrine:schema:update --force

