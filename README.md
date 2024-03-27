# Ultima
Video game digital distribution service

## Stack:
- Frontend (React) <br>
- Backend (PHP, Symfony framework) <br>
- Database (MySQL) <br>
- RabbitMQ

To run containers with application
~~~shell
docker compose up -d
~~~

To enter docker container with symfony
~~~shell
docker exec -it ultima_php_1 sh
~~~

To set up test databse and fixtures
~~~shell
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --env=test --force
php bin/console doctrine:fixtures:load --env=test --group=CheckRelationFixtures
~~~

To run tests
~~~shell
php bin/phpunit
~~~

To run tests with coverage and report
~~~shell
php -d xdebug.mode=coverage bin/phpunit --process-isolation --coverage-clover=tests/coverage.xml --log-junit=tests/report.xml
~~~

