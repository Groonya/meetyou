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

meetyou-init: meetyou-composer-install meetyou-wait-db meetyou-migrations meetyou-fixtures meetyou-assets-build

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

meetyou-fixtures:
	docker-compose run --rm meetyou-php-cli php bin/console fixtures:load --no-debug

meetyou-assets-watch:
	docker-compose run --rm meetyou-node yarn install
	docker-compose run --rm meetyou-node yarn watch

meetyou-assets-build:
	docker-compose run --rm meetyou-node yarn install
	docker-compose run --rm meetyou-node yarn build

build-production:
	docker build --pull --file=meetyou/docker/prod/mysql-master.docker -t ${REGISTRY_ADDRESS}/meetyou-mysql-master:${IMAGE_TAG} meetyou
	docker build --pull --file=meetyou/docker/prod/nginx.docker -t ${REGISTRY_ADDRESS}/meetyou-nginx:${IMAGE_TAG} meetyou
	docker build --pull --file=meetyou/docker/prod/php-fpm.docker -t ${REGISTRY_ADDRESS}/meetyou-php-fpm:${IMAGE_TAG} meetyou
	docker build --pull --file=meetyou/docker/prod/php-cli.docker -t ${REGISTRY_ADDRESS}/meetyou-php-cli:${IMAGE_TAG} meetyou

push-production:
	docker push ${REGISTRY_ADDRESS}/meetyou-mysql-master:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/meetyou-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/meetyou-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/meetyou-php-cli:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose.prod.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MEETYOU_APP_SECRET=${MEETYOU_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MEETYOU_DB_PASSWORD=${MEETYOU_DB_PASSWORD}" >> .env'
