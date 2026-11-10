#! /bin/bash
# deploy

php bin/console c:w

rm -rf var vendor assets/vendor
composer install --no-dev --optimize-autoloader

php bin/console importmap:install
php bin/console minify:install
php bin/console asset-map:compile --env=prod
php bin/console cache:clear

php bin/console doctrine:schema:update --force

