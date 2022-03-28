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
	bin/console d:s:d --force
	bin/console d:s:c
	bin/console d:s:u --force

db-fixtures:
	bin/console d:f:l -n