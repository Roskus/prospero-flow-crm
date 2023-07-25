#!/bin/bash

DOCKER_PHP = crm-php

help: ## Show this help menu
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

build: ## Create docker build containers
	docker-compose build

up: ## Start docker container
	docker-compose up -d

down: ## Stop docker container
	docker-compose down

ssh: ## Connect into php container
	docker exec -it ${DOCKER_PHP} /bin/bash

composer-install: ## Run composer install inside container
	docker exec -it ${DOCKER_PHP} composer install

composer-update: ## Run composer update inside container
	docker exec -it ${DOCKER_PHP} composer update

migrate: ## Run Laravel migrations
	docker exec -it ${DOCKER_PHP} php artisan migrate --no-interaction

seed: ## Run Laravel seeders
	docker exec -it ${DOCKER_PHP} php artisan db:seed

test: ## Run Laravel phpunit tests
	docker exec -it ${DOCKER_PHP} php artisan test

install: ## Setup local enviroment
	cp .env.example .env
	$(MAKE) build
	$(MAKE) up
	$(MAKE) composer-install
	docker exec -it ${DOCKER_PHP} php artisan key:generate
	$(MAKE) migrate
	$(MAKE) seed
	docker exec -it ${DOCKER_PHP} php artisan jwt:secret
