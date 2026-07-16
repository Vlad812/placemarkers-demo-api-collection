API_PLACEMARKERS_COLLECTION_DIR := $(patsubst %/,%,$(dir $(abspath $(lastword $(MAKEFILE_LIST)))))
PROJECT_ROOT := $(abspath $(API_PLACEMARKERS_COLLECTION_DIR)/../..)
include $(PROJECT_ROOT)/config.mk

.PHONY: api-placemarkers-collection-init api-placemarkers-collection-build api-placemarkers-collection-up api-placemarkers-collection-down api-placemarkers-collection-test-unit

api-placemarkers-collection-init:
	@echo "composer зависимости"
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-collection-php-cli composer install --optimize-autoloader --no-interaction
	@echo 'Обновляю автозагрузчик Composer...';
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-collection-php-cli composer dump-autoload --optimize;
	@echo 'Генерирую JWT ключи...';
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-collection-php-cli php bin/console lexik:jwt:generate-keypair --skip-if-exists

api-placemarkers-collection-build:
	@echo build api-placemarkers-collection
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) build

api-placemarkers-collection-up:
	@echo up api-placemarkers-collection
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) up -d api-placemarkers-collection

api-placemarkers-collection-down:
	@echo down api-placemarkers-collection
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) down -v

api-placemarkers-collection-test-unit:
	docker compose -f $(API_PLACEMARKERS_COLLECTION_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-collection-php-cli vendor/bin/phpunit
