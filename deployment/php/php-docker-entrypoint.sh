#!/bin/bash
set -e

echo "🔄 Running migrations..."
if [[ $APP_ENV -ne "local" ]]; then
    doppler run -- php artisan migrate --force
else
    php artisan migrate
fi


echo "✨ Clearing Laravel caches and optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

echo "🚀 Starting php-fpm..."
if [[ $APP_ENV -ne "local" ]]; then
    exec doppler run -- php-fpm
else
    exec php-fpm
fi
