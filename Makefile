up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear meetyou-clear docker-pull docker-build docker-up meetyou-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

meetyou-init: meetyou-composer-install meetyou-wait-db meetyou-migrations meetyou-assets-build

meetyou-bash:
	docker-compose run --rm meetyou-php-cli bash

meetyou-clear:
	docker run --rm -v ${PWD}/meetyou:/app --workdir=/app alpine rm -f .ready

meetyou-composer-install:
	docker-compose run --rm meetyou-php-cli composer install

meetyou-wait-db:
	until docker-compose exec -T meetyou-mysql mysql -u root -pmeetyou  -e ";" ; do sleep 1 ; done

meetyou-migrations:
	docker-compose run --rm meetyou-php-cli php bin/console doctrine:migrations:migrate --no-interaction

meetyou-assets-watch:
	docker-compose run --rm meetyou-node yarn install
	docker-compose run --rm meetyou-node yarn watch

meetyou-assets-build:
	docker-compose run --rm meetyou-node yarn install
	docker-compose run --rm meetyou-node yarn build