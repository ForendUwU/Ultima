apt-get update
apt-get install unzip wget nodejs

wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.6.0.2311.zip
unzip sonar-scanner-cli-4.6.0.2311.zip

./vendor/bin/phpunit --stop-on-failure

sonar-scanner-4.6.0.2311/bin/sonar-scanner