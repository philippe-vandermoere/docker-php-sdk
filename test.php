<?php

declare(strict_types=1);

use PhilippeVandermoere\DockerPhpSdk\DockerFactory;
use \PhilippeVandermoere\DockerPhpSdk\Image\TarStream;

require __DIR__ . '/vendor/autoload.php';

$dockerService = DockerFactory::createDockerService(DockerFactory::createDockerClient());

$dockerService->getImageService()->build(
    new TarStream('/home/philippe/project/hello-world', TarStream::TAR_COMPRESSION_BZIP2),
    [],
    'docker/nginx/Dockerfile'
);

$dockerService->getImageService()->build(
    new TarStream('/home/philippe/project/hello-world', TarStream::TAR_COMPRESSION_GZIP),
    [],
    'docker/php/Dockerfile'
);
