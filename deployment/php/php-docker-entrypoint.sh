#!/bin/bash
set -e

# Get DB details from Doppler first
echo "🔍 Getting database details from Doppler..."
eval $(doppler secrets download --format env --no-file)

# Now we can use DB_HOST and DB_PORT from Doppler
echo "🔄 Waiting for database at $DB_HOST:$DB_PORT..."
while ! nc -z $DB_HOST $DB_PORT; do
  sleep 1
  echo "Still waiting for database..."
done

echo "✨ Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

echo "🔄 Running migrations..."
doppler run -- php artisan migrate --force

echo "🚀 Starting php-fpm..."
exec php-fpm

