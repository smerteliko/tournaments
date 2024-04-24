##################
# Variables
##################

DOCKER_COMPOSE = docker compose -f ./docker-compose.yml
DOCKER_COMPOSE_PHP_FPM_EXEC = ${DOCKER_COMPOSE} exec -u www-data php-fpm

##################
# Docker compose
##################

dc_build:
	${DOCKER_COMPOSE} build

dc_start:
	${DOCKER_COMPOSE} start

dc_stop:
	${DOCKER_COMPOSE} stop

dc_up:
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_down:
	${DOCKER_COMPOSE} down -v --rmi=all --remove-orphans

dc_ps:
	${DOCKER_COMPOSE} ps

dc_logs:
	${DOCKER_COMPOSE} logs -f

restart: dc_stop dc_start
rebuild: dc_down dc_build dc_up


app_bash:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bash
php: app_bash

app_composer_install:
	${DOCKER_COMPOSE} exec -u www-data php-fpm composer install

app_composer_update:
	${DOCKER_COMPOSE} exec -u www-data php-fpm composer update

cache:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console cache:clear
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console cache:clear --env=test

##################
# Database
##################

db_migrate:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console doctrine:migrations:migrate --no-interaction

db_diff:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console doctrine:migrations:diff --no-interaction

db_schema_validate:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console doctrine:schema:validate

db_migration_down:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console doctrine:migrations:execute "App\Shared\Infrastructure\Database\Migrations\Version********" --down --dry-run


db_drop:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bin/console doctrine:schema:drop --force


app_build: dc_build dc_up app_composer_install diff migrate

app_down: db_drop  dc_down

