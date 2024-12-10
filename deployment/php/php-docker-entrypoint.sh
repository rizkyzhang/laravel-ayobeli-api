#!/bin/bash
set -e

eval $(doppler secrets download --format env --no-file)

echo "🔄 Running migrations..."
php artisan migrate --force

echo "✨ Clearing Laravel caches and optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

echo "🚀 Starting php-fpm..."
exec php-fpm

