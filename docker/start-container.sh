#!/bin/sh

set -e

cd /var/www/html

mkdir -p \
    /tmp/kompyuter-tarmoqlari/framework/views \
    /tmp/kompyuter-tarmoqlari/framework/cache \
    /tmp/kompyuter-tarmoqlari/framework/sessions

chown -R www-data:www-data /tmp/kompyuter-tarmoqlari
chmod -R 775 /tmp/kompyuter-tarmoqlari

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction
fi

if [ ! -f vendor/autoload.php ] || [ composer.lock -nt vendor/autoload.php ]; then
    composer install --no-interaction
fi

if [ ! -d node_modules ] || [ ! -f node_modules/.package-lock-ready ] || [ package-lock.json -nt node_modules/.package-lock-ready ] || [ package.json -nt node_modules/.package-lock-ready ]; then
    npm install
    touch node_modules/.package-lock-ready
fi

if grep -q '^APP_KEY=$' .env 2>/dev/null; then
    php artisan key:generate --force
fi

php-fpm -F
