about:
	bin/console about

install:
	composer install
	pnpm install

build:
	pnpm encore prod

build-dev:
	pnpm encore dev

build-dev-watch:
	pnpm encore dev --watch

db-init:
	bin/console d:s:d --force --env=dev
	bin/console d:s:c --env=dev
	bin/console d:s:u --force --env=dev

db-fixtures:
	bin/console d:f:l -n --env=dev

tests-prepare:
	bin/console d:s:d --force --env=test
	bin/console d:s:c --env=test
	bin/console d:s:u --force --env=test
	bin/console d:f:l -n --env=test