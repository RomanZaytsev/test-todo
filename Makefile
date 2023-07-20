down:
	@docker-compose down -v;

stop:
	@docker kill $(docker ps -q)

build:
	@docker-compose build

in.fpm:
	@docker-compose exec fpm su root -s /bin/bash

in.nginx:
	@docker-compose exec nginx su root -s /bin/bash

ps:
	@docker ps -a --filter "name=todo-php"

up:
	@docker-compose up -d
	@docker/waitContainer.sh "(todo-php).(fpm)"
	@make composer.install
	@docker/waitContainer.sh "(todo-php).(mysql)"

backup:
	@docker-compose exec mysql mysqldump --skip-lock-tables --routines --databases --add-drop-database --add-drop-table --disable-keys --extended-insert --events --comments -u root -proot todo-php > docker/mysql/init/02-todo-php.sql

composer.install:
	@docker-compose exec fpm su root -c "composer install --optimize-autoloader;"

test:
	@./docker/tests/run-tests.sh