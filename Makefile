#!/bin/bash

DOCKER_PHP = crm-php

help: ## Show this help menu
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-20s %s\n", $$1, $$2}'

build: ## Create docker build containers (no database)
	docker compose -f docker-compose.yml build

build-maria: ## Create docker build containers with MariaDB
	docker compose -f docker-compose.yml -f docker-compose.mysql.yml -f docker-compose.pma.yml build

build-pg: ## Create docker build containers with Postgres
	docker compose -f docker-compose.yml -f docker-compose.postgres.yml -f docker-compose.pgadmin.yml build

build-ms: ## Create docker build containers with MS SQL Server
	docker compose -f docker-compose.yml -f docker-compose.mssql.yml build

up: ## Start docker container without db
	docker compose -f docker-compose.yml up -d

up-mysql: ## Start docker container with MariaDB / MySQL
	docker compose -f docker-compose.yml -f docker-compose.mysql.yml -f docker-compose.pma.yml up -d

up-pg: ## Start docker container with Postgres
	docker compose -f docker-compose.yml -f docker-compose.postgres.yml -f docker-compose.pgadmin.yml up -d

up-ms: ## Start docker container with MS SQL Server
	docker compose -f docker-compose.yml -f docker-compose.mssql.yml up -d

permissions: ## Fix directory permissions for www-data
	docker exec -u root ${DOCKER_PHP} mkdir -p /var/www/.composer/cache
	docker exec -u root ${DOCKER_PHP} chown -R www-data:www-data /var/www/crm /var/www/.composer
	docker exec -u root ${DOCKER_PHP} chmod -R 775 /var/www/crm/storage /var/www/crm/bootstrap/cache /var/www/crm/vendor

ssl: ## Generate self-signed SSL certificates for local development
	openssl req -x509 -newkey rsa:2048 -nodes -days 3650 \
		-keyout infrastructure/etc/nginx/ssl/private/localhost.key \
		-out infrastructure/etc/nginx/ssl/certs/localhost.crt \
		-subj "/CN=localhost"

down: ## Stop docker container
	docker compose down

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

test-coverage: ## Run tests with HTML coverage report (requires Xdebug)
	docker exec -e XDEBUG_MODE=coverage -it ${DOCKER_PHP} vendor/bin/phpunit --coverage-html=storage/coverage

clear: ## Clear all Laravel caches (views, config, cache)
	docker exec -it ${DOCKER_PHP} php artisan cache:clear
	docker exec -it ${DOCKER_PHP} php artisan view:clear
	docker exec -it ${DOCKER_PHP} php artisan config:clear

install: ## Setup local enviroment with MariaDB
	cp .env.example .env
	$(MAKE) ssl
	$(MAKE) build-maria
	$(MAKE) up-mysql
	$(MAKE) permissions
	$(MAKE) composer-install
	docker exec -it ${DOCKER_PHP} php artisan key:generate
	$(MAKE) migrate
	$(MAKE) seed
	docker exec -it ${DOCKER_PHP} php artisan jwt:secret
