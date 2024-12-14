#!/bin/bash
set -e

echo "🔄 Running migrations..."
if [[ $APP_ENV -ne "local" ]]; then
#    doppler run -- php artisan migrate --force

    echo "✨ Clearing Laravel caches and optimizing..."
#    php artisan optimize

    echo "🚀 Starting php-fpm..."
    exec doppler run -- php-fpm
else
    php artisan migrate

    echo "🚀 Starting php-fpm..."
    exec php-fpm
fi
