#!/bin/bash

# Exit on any error
set -e
# Exit on pipe failure (if any command in a pipe fails)
set -o pipefail
# Echo commands before running them (optional, helpful for debugging)
set -x

git checkout main
git pull origin main

echo "Compose down"
docker compose -f docker-compose.yaml -f docker-compose.prod.yaml down
echo "Compose up"
docker compose -f docker-compose.yaml -f docker-compose.prod.yaml up --build -d --remove-orphans
echo "Run migrations"
docker exec $DOCKER_CONTAINER_NAME php artisan migrate
echo "Optimize application"
docker exec $DOCKER_CONTAINER_NAME php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan optimize
