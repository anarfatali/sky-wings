#!/bin/bash
docker-compose up --build

sleep 10

docker-compose exec app php artisan migrate

docker-compose exec app php artisan storage:link
