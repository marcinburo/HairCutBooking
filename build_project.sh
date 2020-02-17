#!/bin/bash

docker-compose up -d
docker-compose exec app sh -c "composer install"
docker-compose exec app sh -c "yarn install"
docker-compose exec app sh -c "yarn encore dev"
docker-compose exec app sh -c "php bin/console doctrine:migrations:migrate"
docker-compose exec app sh -c "php bin/console doctrine:fixtures:load"