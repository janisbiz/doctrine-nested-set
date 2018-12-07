SHELL=/bin/bash
DOCKER_COMPOSE ?= docker-compose
DOCKER_COMPOSE_EXEC_PHP = $(DOCKER_COMPOSE) exec php

.DEFAULT_GOAL := help

.PHONY: help
help:
	grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "[32m%-12s[0m %s\n", $$1, $$2}'

.PHONY: test
test: ## Run tests
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/phpcs -p ./src --standard=PHPCompatibility,PSR2 --runtime-set testVersion 7.0
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/phpstan analyse -l 2 src
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/behat
