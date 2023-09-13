init: docker-build docker-up composer-install

docker-up:
	docker-compose up -d

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm php composer install
