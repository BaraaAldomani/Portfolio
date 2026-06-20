# Shortcuts for the dockerized dev environment.
# Usage examples:
#   make up                      start everything (build if needed)
#   make down                    stop everything
#   make sh                      shell into the app (php-fpm) container
#   make artisan cmd="route:list"
#   make composer cmd="require foo/bar"
#   make npm cmd="run build"
#   make migrate | make fresh | make logs | make ps

COMPOSE = docker compose

.PHONY: up down build sh artisan migrate fresh composer npm logs ps

up:
	$(COMPOSE) up -d --build

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

sh:
	$(COMPOSE) exec app sh

artisan:
	$(COMPOSE) exec app php artisan $(cmd)

migrate:
	$(COMPOSE) exec app php artisan migrate

fresh:
	$(COMPOSE) exec app php artisan migrate:fresh --seed

composer:
	$(COMPOSE) exec app composer $(cmd)

npm:
	$(COMPOSE) run --rm node npm $(cmd)

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps
