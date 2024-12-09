#!/bin/bash
set -e

# Get DB details from Doppler first
echo "🔍 Getting database details from Doppler..."
eval $(doppler secrets download --format env --no-file | grep DB_)

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

php artisan tinker --execute="echo json_encode(config('database.connections'), JSON_PRETTY_PRINT);"

echo "🔄 Running migrations..."
php artisan migrate --force

echo "🚀 Starting php-fpm..."
exec php-fpm

