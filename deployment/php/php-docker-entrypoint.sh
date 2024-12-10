#!/bin/bash
set -e

echo "🔄 Running migrations..."
doppler run -- php artisan migrate --force

echo "✨ Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "🚀 Starting php-fpm..."
exec doppler run -- php-fpm

