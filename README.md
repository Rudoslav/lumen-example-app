# Conveyor server

This project runs as a set of docker containers.

For application related information, see `lumen-app/README.md`

If you want to use xdebug. Change `xdebug_mode` in `docker/app/conf.d/xdebug.ini`
to: 
```
xdebug.mode=develop,debug
```
## Docker containers
* **nginx**
  * webserver, that connects clients to our app container running php-fpm
* **app**
  * this container includes:
    * TBD - box assignment api (picker app API)
    * TBD - expected order weight sync process (from magento)
    * TBD - measured/real order weight sync process (to magento)
  * it runs a lumen application framework
* **conveyor_tcp**
  * runs a TCP server, that accepts measured weight from conveyor belt
  * this runs as another lumen instance
* **db**
  * shared DB for both **app** and **conveyor_tcp** applications

## First time start
1. create env files:
   1. for docker `cd docker && cp .env.example .env`
   2. for app `cd lumen-app && cp .env.example .env`
2. build `cd docker && docker-compose build`
3. Enter `app` container `docker-compose exec -u www-data app sh` and run `composer install`
4. In `app` container run `php artisan migrate`
5. Enter `app` container `docker-compose exec app sh` and run `chown -R www-data:www-data storage` and `chown www-data:www-data /var/log/cron.log`

## Every other start
* start containers: `cd docker && docker-compose up`
* to enter the app container: `cd docker && docker-compose exec -u www-data app sh`

## Git hooks
To enable local git hooks, run:
```
git config core.hooksPath .githooks
```
This sets your default hooks path to .githooks/, where our hooks are stored.

## Debugging common problems
* socket 0.0.0.0:3306 is already taken
  * stop host machine's mysql by `service mysql stop`, or change exposed port in `docker-compose.yml`
* DNS is not working in container
  * disconnect from VPN & down/up docker containers

## Xdebug PHPStorm setup
Currently only "Start Listening for PHP debug Connections" works.