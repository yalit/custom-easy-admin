about:
	bin/console about

install:
	composer install

build:
	bin/console asset-map:compile

db-init-dev:
	bin/console d:s:d --env=dev --force
	bin/console d:d:c --env=dev
	bin/console d:s:c --env=dev
	bin/console d:s:u --force --env=dev

db-fixtures:
	bin/console d:f:l -n --env=dev

tests-prepare:
	bin/console d:s:d --env=test --force
	bin/console d:d:c --env=test
	bin/console d:s:c --env=test
	bin/console d:s:u --force --env=test
	bin/console d:f:l -n --env=test
	vendor/bin/bdi detect drivers

test:
	bin/phpunit

lint-yaml:
	./bin/console lint:yaml config --parse-tags

lint-twig:
	./bin/console lint:twig templates --env=prod

lint-translations:
	./bin/console lint:xliff translations

lint-config:
	./bin/console lint:container --no-debug

lint-doctrine:
	./bin/console doctrine:schema:validate --skip-sync -vvv --no-interaction

lint:
	make lint-yaml
	make lint-twig
	make lint-translations
	make lint-config
	make lint-doctrine

composer-validate:
	composer validate --strict

check-security:
	symfony check:security

analyze:
	make lint
	make composer-validate
	make check-security
