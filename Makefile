.PHONY: help build up down restart logs shell composer install migrate seed test clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build --no-cache

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop and remove Docker containers
	docker-compose down

restart: ## Restart Docker containers
	docker-compose restart

logs: ## View container logs
	docker-compose logs -f

shell: ## Access app container shell
	docker-compose exec app bash

composer: ## Run composer install
	docker-compose exec app composer install

install: ## Full installation (composer, key, migrate, permissions)
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app chmod -R 775 storage bootstrap/cache
	docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

test: ## Run PHPUnit tests
	docker-compose exec app php artisan test

clean: ## Clean cache and config
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan route:clear

fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

db: ## Access MySQL database
	docker-compose exec db mysql -u irequest -proot irequest

redis: ## Access Redis CLI
	docker-compose exec redis redis-cli

