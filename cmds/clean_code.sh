#! /bin/bash
# clean code

bin/biome check --write assets/app.js assets/util.js assets/pages

php bin/console c:w

php bin/console lint:container
winpty php vendor/bin/grumphp run --tasks=phpparser
vendor/bin/php-cs-fixer fix
vendor/bin/phpstan analyse
winpty php vendor/bin/grumphp run --tasks=securitychecker_symfony
php bin/console lint:twig templates
