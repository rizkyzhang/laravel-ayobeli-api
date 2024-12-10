#!/bin/bash
set -e

echo "🔄 Running migrations..."
doppler run -- php artisan migrate --force

echo "✨ Clearing Laravel caches and optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

echo "🚀 Starting php-fpm..."
exec doppler run -- php-fpm
