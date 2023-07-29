# BileMo - Project install

## Requirements

Database & database interface (like phpmyadmin, workbench, adminer ...)
php 8^
symfony 6.2^
Postman (or Curl)
OpenSSL (or a terminal that already include OpenSSL, like Gitbash etc...)

## Download the projet

Terminal command : "git clone "https://github.com/requenaGuillaume/BileMo.git"
or
Go to https://github.com/requenaGuillaume/BileMo and choose another way to get the project (download zip folder etc..)

## Run server

Use the "symfony serve" command in terminal (from the folder project)

## Install dependecies

Run the terminal command : "composer install"

## Create database

Create database using terminal command : "symfony console d:d:c"
Run the migrations using terminal command : "symfony console doctrine:migrations:migrate"

## Fixtures

Run the fixtures using terminal command : "symfony console d:f:l"

## LexikJWT for the JWT

(require OpenSSL)

Change JWT_PASSPHRASE in file .env.local

Generate private token in a config/jwt/private.pem file with terminal command :  
"openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096"
Then enter your pass phrase

Generate public token in a config/jwt/public.pem file with terminal command :
"openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout"
Then enter your pass phrase

If pem files has not been created, create the manually and redo the previous commands.

## Api classic rules

Do not forget "Content-Type application/json", "Authorization" : "bearer YourToken" etc...

## You're done

Project must be ready now.
