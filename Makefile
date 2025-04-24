.PHONY: build up down restart install shell composer php logs migrate

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d

install:
	docker-compose exec app composer install

shell:
	docker-compose exec app sh

composer:
	docker-compose exec app composer

php:
	docker-compose exec app php

logs:
	docker-compose logs -f

migrate:
	docker-compose exec app php bin/console doctrine:migrations:migrate
