# tdd-phpunit improving

seeking to improve my unit tests with phpunit.

## description

This is forked from https://github.com/viniciuswebdev/curso-php-phpunit, the "Testes unitários e TDD com PHP e PHPUnit" (Unit Tests and TDD with PHP and PHPUnit) course instructor.

In his project, he created a pseudo-framework to simulate an environment more complex than the usual unit tests exemples.

In my forked project, I'm going to work in the "parte_2" directory, where there is this pseudo-framework, using docker to set up a basic php8 environment.

## instalation

1. Clone:

`git clone git@github.com:pauloafonso/curso-php-phpunit.git`

2. Go to the "parte_2" directory

3. Bulding the php8 docker image:

`docker build -t tdd-php8-image .`

4. Creating and running the container:

`docker run -d --restart=unless-stopped --mount type=bind,source="$(pwd)",target=/var/www/tdd-improving --name tdd-improving tdd-php8-image`

5. Installing the composer dependencies (phpunit) and running the pseudo-framework autoloader

`docker exec -w /var/www/tdd-improving tdd-improving composer install`

6. Executing the tests:

`docker exec tdd-improving ./vendor/bin/phpunit src/`