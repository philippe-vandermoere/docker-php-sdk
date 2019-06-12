![PHP from Packagist](https://img.shields.io/packagist/php-v/philippe-vandermoere/docker-php-sdk.svg)
![GitHub tag (latest SemVer)](https://img.shields.io/github/tag/philippe-vandermoere/docker-php-sdk.svg)
![CircleCI](https://img.shields.io/circleci/build/github/philippe-vandermoere/docker-php-sdk.svg)

# Docker-php-sdk

## Installation

```
composer require philippe-vandermoere/docker-php-sdk
```

## Usage

### Socket
```
use PhilippeVandermoere\DockerPhpSdk\DockerFactory;

$dockerService = DockerFactory::createDockerService(
    DockerFactory::createSocketDockerClient(DockerFactory::DOCKER_SOCKET_PATH)
);
```

### TCP
```
use PhilippeVandermoere\DockerPhpSdk\DockerFactory;

$dockerService = DockerFactory::createDockerService(
    DockerFactory::createTCPDockerClient(DockerFactory::DOCKER_TCP_HOST, DockerFactory::DOCKER_TCP_PORT)
);
```
