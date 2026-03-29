.PHONY: calc build up down shell install update cf phpstan unit behat behat-debug lint test qa

calc:
	cd .dev && docker-compose exec app php bin/calculate "$(amount)" "$(term)"

# DOCKER
setup: build up install
	@echo "Development environment is ready!"

build:
	cd .dev && docker-compose build

up:
	cd .dev && docker-compose up -d

down:
	cd .dev && docker-compose down

shell:
	cd .dev && docker-compose exec app bash

# DEPENDENCIES
install:
	cd .dev && docker-compose exec app composer install

update:
	cd .dev && docker-compose exec app composer update

# CODE QUALITY TOOLS
cf:
	cd .dev && docker-compose exec app vendor/bin/php-cs-fixer fix --config=.dev/.php-cs-fixer.php

phpstan:
	cd .dev && docker-compose exec app vendor/bin/phpstan analyse --configuration=.dev/phpstan.neon

unit:
	cd .dev && docker-compose exec app vendor/bin/phpunit

unit-file:
	cd .dev && docker-compose exec -e XDEBUG_SESSION=1 app php -d xdebug.mode=develop,debug vendor/bin/phpunit $(FILE)

behat:
	cd .dev && docker-compose exec app vendor/bin/behat

behat-debug:
	cd .dev && docker-compose exec app vendor/bin/behat --verbose --no-interaction --stop-on-failure

lint: cf phpstan

test: unit behat

qa: lint test

