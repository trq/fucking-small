# Fucking Small Framework

## What is this?

A fucking small web application framework built with zero dependencies.

## What do you get?

### A sample application in ./app

#### Run it using PHP's web server:

    php -S localhost:8000 -t app/web app/bootstrap.php

### A framework consisting of the following components

* A IoC container capable of resolving attached services as well as auto
  resolving dependencies not attached to the container via reflection

* A Router capable of mapping patterns within urls to controller actions and
  parameters

* Automatic dependency injection of all arguments (via type hinting, the
  container and parameters discovered via the router / dispatcher) into
  controller methods

* A tiny (not at all complete) HTTP Request/Response abstraction

## How do I use?

Take a look at the code in the sample app and the tests.

## Running the test suite

### Prerequisites

Composer:

    curl -sS https://getcomposer.org/installer | php

PHPUnit:

    php ./composer.phar install

### Run the suite:

    ./vendor/bin/phpunit
