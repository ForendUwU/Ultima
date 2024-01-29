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

To set up test databse and fixtures
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

To run tests
~~~shell
php bin/phpunit --process-isolation
~~~

To run tests with coverage and report
~~~shell
php -d xdebug.mode=coverage bin/phpunit --process-isolation --coverage-clover=tests/coverage.xml --log-junit=tests/report.xml
~~~

To download SonarScanner
~~~shell
export SONAR_SCANNER_VERSION=5.0.1.3006
export SONAR_SCANNER_HOME=$HOME/.sonar/sonar-scanner-$SONAR_SCANNER_VERSION-linux
curl --create-dirs -sSLo $HOME/.sonar/sonar-scanner.zip https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-$SONAR_SCANNER_VERSION-linux.zip
unzip -o $HOME/.sonar/sonar-scanner.zip -d $HOME/.sonar/
export PATH=$SONAR_SCANNER_HOME/bin:$PATH
export SONAR_SCANNER_OPTS="-server"
~~~

To use SonarScanner
~~~shell
sonar-scanner
~~~

