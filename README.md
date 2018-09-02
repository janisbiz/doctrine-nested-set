# Doctrine nested set

Adds a functionality for Doctrine, so it can be used by nested set model defined 
[here](https://en.wikipedia.org/wiki/Nested_set_model).

## Getting started

These instructions will help you set up this doctrine extension, so you can use it. As well, there are provided 
instructions to run integration tests for the extension.

## Using extension

To use extension:
 - your entity class should implement [NestedSetEntityInterface](src/Entity/NestedSetEntityInterface.php)
 - your repository class should extend [NestedSetEntityRepository](src/Repository/NestedSetEntityRepository.php)

Afterwards you can use helper functions from [NestedSetEntityRepository](src/Repository/NestedSetEntityRepository.php),
which are sufficient for full operations on nested set tree model.

## Running tests

There are two ways to run tests:
1) By using docker containers:
    - Copy `.env.dist` to `.env` and adjust defined values for your needs
    - Execute `docker-compose up -d --build`
    - Execute `docker-compose exec php composer install`
    - Execute `docker-compose exec php vendor/bin/behat`
2) By using your local php and mysql database environment:
    - Ensure, that your php version is `7.1.x`
    - Install `pdo_mysql` extension for php
    - Adjust database connection in [configuration file](src/Tests/Features/Bootstrap/Resources/config/doctrine.yaml)
    - Execute `composer install`
    - Execute `vendor/bin/behat`
