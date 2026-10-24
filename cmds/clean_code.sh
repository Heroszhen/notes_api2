#! /bin/bash

php bin/console c:w

winpty php vendor/bin/grumphp run --tasks=phpparser
vendor/bin/php-cs-fixer fix
vendor/bin/phpstan analyse
winpty php vendor/bin/grumphp run --tasks=securitychecker_symfony


php bin/console lint:twig templates