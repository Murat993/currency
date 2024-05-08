init: manager-clear docker-build docker-up composer-install set-permissions manager-ready

docker-up:
	docker-compose up -d

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm php composer install

set-permissions:
	chmod -R 777 storage

manager-ready:
	docker run --rm -v ${PWD}/:/var/www --workdir=/var/www alpine touch .ready

manager-clear:
	docker run --rm -v ${PWD}/:/var/www --workdir=/var/www alpine rm -f .ready
