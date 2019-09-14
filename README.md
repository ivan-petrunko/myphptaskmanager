# myphptaskmanager

Simple PHP task manager based on MVC pattern with minimum dependencies.

## [View demo](http://myphptaskmanager.petrunko.com/) (in Russian)

## Requirements
* PHP >=7.2 with ext-gd, ext-json, ext-pdo, ext-mbstring.
* MySQL 5.7
* Docker

## Dependencies
* [`composer.json`](./composer.json)
* [jquery](https://jquery.com/)
* [bootstrap](https://jquery.com/)  

## Set up
* Set up Docker into your OS - [RTFM](https://docs.docker.com/).
* Clone this repository.
* Install dependencies via composer
```bash
composer install
```
* Create & modify `.env`
```bash
cp .env.example .env
$EDITOR .env
```
* Run docker
```bash
docker-compose up
```
* Set up MySQL
```bash
source .env
mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASSWORD < src/Migrations/up.sql
```
* Go to [http://localhost:81]([http://localhost:81])
* Enjoy!

## TODO
* Refactor. This is just a demo.
* Security: [RBAC](https://en.wikipedia.org/wiki/Role-based_access_control).
* Deployment. Oh, it really exists & works via 3 bash scripts =) Maybe I'll publish them later: 
    * `create_build.sh` - Creates local build in `tar.xz` format.
    * `deploy.sh` - Uploads `tar.xz` to web server & executes `deploy_on_server.sh` on web server. 
    * `deploy_on_server.sh` - Unpacks `tar.xz` on web server, creates new symlink to currently deployed build.
