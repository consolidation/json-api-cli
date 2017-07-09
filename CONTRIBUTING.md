# Contributing to JSON API CLI

Thank you for your interest in contributing to the JSON API CLI!  This project aims to provide reusable, loosely-coupled components useful for building automation tools for testing and deployment. This CLI is built on top of [Robo PHP](https://robo.li/framework).

Here are some of the guidelines you should follow to make the most of your efforts:

## Code Style Guidelines

JSON API CLI adheres to the [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/) for PHP code.

## Pull Request Guidelines

Every pull request is run through:

  - phpcs -n --standard=PSR2 src
  - phpunit
  - [Scrutinizer](https://scrutinizer-ci.com/g/consolidation/annotated-command/)
  
It is easy to run the unit tests and code sniffer locally; just run:

  - composer cs

To run the code beautifier, which will fix many of the problems reported by phpcs:

  - composer cbf

These two commands (`composer cs` and `composer cbf`) are defined in the `scripts` section of [composer.json](composer.json).

After submitting a pull request, please examine the Scrutinizer report. It is not required to fix all Scrutinizer issues; you may ignore recommendations that you disagree with. The spacing patches produced by Scrutinizer do not conform to PSR2 standards, and therefore should never be applied. DocBlock patches may be applied at your discression. Things that Scrutinizer identifies as a bug nearly always need to be addressed.

Pull requests must pass phpcs and phpunit in order to be merged; ideally, new functionality will also include new unit tests.
