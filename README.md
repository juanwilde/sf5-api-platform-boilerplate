# Symfony 5 + API Platform + Docker boilerplate

## What is this?
This is a boilerplate project which includes `Symfony 5.0`, `API Platform` and a `Docker configuration`.

It contains all the features needed to start building an API:
- Authentication system with Json Web Tokens (JWT) (https://github.com/lexik/LexikJWTAuthenticationBundle)
- Security configuration using Symfony voters (https://symfony.com/doc/current/security/voters.html)
- Basic configuration for resources and serialization groups for API Platform
- Endpoints to manage users (register, get, update, delete)
![Endpoints](./docs/endpoints.png)
- Fixtures for testing
- Unit test for the Register action
- Functional tests to cover the user endpoints use cases

IMPORTANT: to run functional tests access the database container and create a database called `database_test` or create your own configuration. `doctrine/doctrine-fixtures-bundle` and `liip/test-fixtures-bundle` are included.


## Usage
- `make build` to build the docker environment
- `make prepare` to install dependencies and run migrations
- `make generate-ssh-keys` to generate JWT certificates
- `http://localhost:500/api/v1/docs` to check the Open API v3 documentation
- `make restart` to stop and start containers
- `make ssh-be` to access the PHP container bash
- `make be-logs` to tail dev logs
- `make code-style` to run PHP-CS-FIXER on src and tests
- `make tests` to run the test suite

## Stack:
- `NGINX` container
- `PHP7.4 FPM` container
- `MySQL 5.7` container + `volume`

## Contributing
Feel free to clone, fork or PR this repo

## Coming soon
- Action to activate users sending an email with an activation code.
- Action to reset user password (forgot password)
- Action to change password

## License
This bundle is under the MIT license.
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.
