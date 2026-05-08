#!/bin/sh

set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install
fi

if [ ! -d node_modules ] || [ ! -f node_modules/.package-lock-ready ]; then
    npm install
    touch node_modules/.package-lock-ready
fi

if grep -q '^APP_KEY=$' .env 2>/dev/null; then
    php artisan key:generate --force
fi

php-fpm
