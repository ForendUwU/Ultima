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
