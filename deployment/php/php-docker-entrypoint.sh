#!/bin/bash
set -e

#echo "✨ Clearing Laravel caches..."
#php artisan config:clear
#php artisan route:clear
#php artisan cache:clear
#php artisan view:clear

echo "🔄 Running migrations..."
doppler run -- php artisan migrate --force

echo "🚀 Starting php-fpm..."
exec php-fpm

