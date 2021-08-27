# Dockerized Symfony 5 API
This project is using symfony 5 to prepare simple REST API that has customers which              
can deposit and retrieve credit.

## Getting started
Clone the repository and move to project root

run composer install.
In the root of the project run ``docker-compose up -d``

When the server is booted run: `docker-compose exec php-fpm php bin/console doctrine:migrations:migrate`
Select `yes` option. The DB migrations will be executed

Visit the running webservice on [localhost:14000](http://localhost:14000)
                                                           
## Solved problems
Used DB locks to support concurrent writes and reads to the DB when editing the 
customer balance

## API documentation
Documentation is available at `api/doc`

## Test coverage
Basic test coverage for bonus calculations and main deposit controller method 

## Used technologies
* Symfony 5
* Php 7.4
* Mysql 8
* Phpunit
* NelmioApiDocBundle
* Docker
                                                   
## API functionalities
* Each new created customer is assigned a random bonus which is set on every 3rd transaction
* Customer can deposit credit
* Customer can withdraw credit
* Bonus credit assigned on every 3rd deposit is counted separately
* There is a report endpoint for the last 7 days transactions