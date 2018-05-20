# roleplaygateway
source code for roleplaygateway.com

## Directory Structure
`styles/RolePlayGateway/template` contains most of the "templates" for pages.

## Tests
We're using PHPUnit to test our existing infrastructure as we prepare for
migration to [Fabric][fabric].  Following [the Maki Philosophy][maki-docs], we
will use a REST API to formally define a list of Resources and their expected
behavior.  From this point, we will use TDD to finalize a 1.0 version of RPG,
then begin writing migration scripts for Fabric.

## Installation
```bash
$ composer require phpunit/phpunit
$ composer require guzzlehttp/guzzle
$ composer update
```

## Running Tests
```
$ composer test
```

## Goals
Next steps of this project are to build a working Docker image of the site that
can be independently deployed with a single command.  [Help wanted][help]!

[fabric]: https://fabric.fm
[maki-docs]: https://maki.io/docs
[help]: https://github.com/roleplaygateway/roleplaygateway/issues?q=is%3Aopen+is%3Aissue+label%3A%22help+wanted%22
