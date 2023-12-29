# Ultima
Video game digital distribution service

To run containers with application
~~~shell
docker compose up -d
~~~

To stop containers
~~~shell
docker compose down
~~~

To clean dokcer images and containers
~~~shell
docker system prune -af
~~~

To enter docker container with symfony
~~~shell
docker exec -it ultima_php_1 sh
~~~
or
~~~shell
docker exec -it ultima_php_1 bash
~~~

To run tests
~~~shell
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --env=test --force
php bin/console doctrine:fixtures:load --env=test --group=CheckRelationFixtures
~~~

You'll get the message
~~~shell
Careful, database "db_test" will be purged. Do you want to continue? (yes/no) [no]:
~~~
say
~~~shell
yes
~~~
