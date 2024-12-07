#!/bin/bash

git checkout develop
git pull origin develop

echo "Compose down"
docker compose -f docker-compose.prod.yaml up down
echo "Compose up"
docker compose -f docker-compose.prod.yaml up --build -d --remove-orphans
echo "Run migrations"
docker exec $DOCKER_CONTAINER_NAME php artisan migrate
echo "Optimize application"
docker exec $DOCKER_CONTAINER_NAME php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan optimize
