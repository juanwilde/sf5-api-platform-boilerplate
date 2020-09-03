#!/bin/bash

DOCKER_BE = sf5-api-platform-docker-be
UID = $(shell id -u)

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

run: ## Start the containers
	$(MAKE) copy-files
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) run

build: ## Rebuilds all the containers
	$(MAKE) copy-files
	U_ID=${UID} docker-compose build

prepare: ## Runs backend commands
	$(MAKE) copy-files
	$(MAKE) composer-install
	$(MAKE) migrations

copy-files: ## Creates a copy of .env and docker-compose.yml.dist file to use locally
	cp -n .env .env.local || true
	cp -n docker-compose.yml.dist docker-compose.yml || true

# Backend commands
composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} -it ${DOCKER_BE} composer install --no-scripts --no-interaction --optimize-autoloader

migrations: ## Runs the migrations
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bin/console doctrine:migrations:migrate -n

be-logs: ## Tails the Symfony dev log
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} tail -f var/log/dev.log
# End backend commands

ssh-be: ## ssh's into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

code-style: ## Runs php-cs to fix code styling following Symfony rules
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix tests --rules=@Symfony

generate-ssh-keys: ## Generate ssh keys in the container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} mkdir -p config/jwt
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl genrsa -passout pass:password -out config/jwt/private.pem -aes256 4096
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl rsa -pubout -passin pass:password -in config/jwt/private.pem -out config/jwt/public.pem

tests: ## Runs Unit and Functional tests
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bin/phpunit

.PHONY: tests migrations copy-files
