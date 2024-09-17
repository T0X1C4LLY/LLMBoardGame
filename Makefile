SHELL := /bin/bash
NAME := chatgpt-dev
FRONT := chatgpt-front-dev
OLLAMA := ollama
EXEC_COMMAND ?= docker-compose exec $(NAME)
EXEC_FRONT_COMMAND ?= docker-compose run $(FRONT)
EXEC_OLLAMA_COMMAND ?= docker-compose exec $(OLLAMA)

install: create_network build up composer npm

build:
	docker-compose build

composer:
	${EXEC_COMMAND} composer install -o

create_network:
	docker network create chatgpt-nginx-proxy || true

up:
	docker-compose up -d

down:
	docker-compose down

stop:
	docker-compose stop

bash:
	${EXEC_COMMAND} $(SHELL)

front:
	${EXEC_FRONT_COMMAND} $(SHELL)

ollama:
	${EXEC_OLLAMA_COMMAND} $(SHELL)

restart: down install

prepare_db:
	${EXEC_COMMAND} bin/console doctrine:database:create --if-not-exists; ${EXEC_COMMAND} bin/console doctrine:migrations:migrate --no-interaction

prepare_db_test:
	${EXEC_COMMAND} bin/console doctrine:database:create --env=test --if-not-exists; ${EXEC_COMMAND} bin/console doctrine:migrations:migrate --no-interaction --env=test

create_migration:
	${EXEC_COMMAND} bin/console doctrine:migrations:diff

migrate:
	${EXEC_COMMAND} bin/console doctrine:migrations:migrate

migrate_test:
	${EXEC_COMMAND} bin/console doctrine:migrations:migrate --env=test

phpcsfixer:
	${EXEC_COMMAND} php -dmemory_limit=-1 vendor/bin/php-cs-fixer --no-interaction --allow-risky=yes --dry-run --diff fix

phpcsfixer_fix:
	${EXEC_COMMAND} php -dmemory_limit=-1 vendor/bin/php-cs-fixer --no-interaction --allow-risky=yes --ansi fix

phpstan:
	${EXEC_COMMAND} vendor/bin/phpstan --level=max --configuration=phpstan.neon analyse src

phpunit:
	${EXEC_COMMAND} ./vendor/bin/phpunit

deptrac:
	${EXEC_COMMAND} vendor/bin/deptrac --report-uncovered

prepare_pr: phpcsfixer_fix phpstan deptrac phpunit

watch:
	${EXEC_FRONT_COMMAND} npm run watch

npm:
	${EXEC_FRONT_COMMAND} npm install --force
	${EXEC_FRONT_COMMAND} npm run dev

serv-dump:
	${EXEC_COMMAND} bin/console server:dump
