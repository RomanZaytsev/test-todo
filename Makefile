down:
	@docker-compose down -v;
#	@yes 'y' | docker builder prune

stop:
	@docker kill $(docker ps -q)

build:
	@docker-compose build

in.fpm:
	@docker-compose exec fpm su root -s /bin/bash

in.nginx:
	@docker-compose exec nginx su root -s /bin/bash

ps:
	@docker ps -a --filter "name=test-todo"

up:
	@docker-compose up -d
	@docker/waitContainer.sh "(test-todo).(mysq)"

backup:
	sudo mysqldump --skip-lock-tables --routines --databases --add-drop-database --add-drop-table --disable-keys --extended-insert --events --comments -u root -p --host=127.0.0.1 --port=33060 test-todo > docker/mysql/init/02-test-todo.sql
