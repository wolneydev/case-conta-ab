#!/bin/sh
set -e

cd /var/www

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan migrate --seed --force

exec php artisan serve --host=0.0.0.0 --port=8000
