# Symfony 5 API Transaction
This project is using symfony 5 to prepare simple API that has customers which              
can deposit and retrieve credit.

## Getting started
Clone the repository and run composer install.
Point your local server with php to the public/index.php
Edit the .env file to use your mysql server.
DATABASE_URL=mysql://<DBusername>:@<location>:3306/<dbName>?serverVersion=8.0
                                                           
## Solved problems
Used DB locks to support concurrent writes and reads to the DB when editing the 
customer balance

## Json documentation
Documentation is available at api/doc.json

## Used technologies
* Symfony 5
* Php 7.4
* Mysql 8
* Phpunit
* NelmioApiDocBundle
                                                   
## API functionalities
* Each new created customer is assigned a random bonus which is set on every 3rd transaction
* Customer can deposit credit
* Customer can withdraw credit
* Bonus credit assigned on every 3rd deposit is counted separately
* There is an report endpoint for the last 7 days transactions