php bin/phpunit 
yarn run eslint assets/js/components/
yarn run eslint assets/js/pages/
./php-cs-fixer --diff --dry-run -v --allow-risky=yes fix
vendor/bin/phpstan analyse src --level 5
