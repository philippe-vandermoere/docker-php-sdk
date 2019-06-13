<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk;

use Http\Client\HttpClient;
use Http\Client\Curl\Client as CurlClient;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerService;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkService;
use PhilippeVandermoere\DockerPhpSdk\Image\ImageService;

class DockerFactory
{
    public const DOCKER_SOCKET_PATH = '/var/run/docker.sock';

    public const DOCKER_TCP_HOST = '127.0.0.1';

    public const DOCKER_TCP_PORT = 2375;

    public static function createSocketDockerClient(string $dockerSocketPath): CurlClient
    {
        return new CurlClient(
            null,
            null,
            [CURLOPT_UNIX_SOCKET_PATH => $dockerSocketPath]
        );
    }

    public static function createTCPDockerClient(string $host): CurlClient
    {
        return new CurlClient(
            null,
            null,
            [CURLOPT_PROXY => $host]
        );
    }

    public static function createDockerService(HttpClient $dockerClient): DockerService
    {
        return new DockerService(
            new ContainerService($dockerClient),
            new NetworkService($dockerClient),
            new ImageService($dockerClient)
        );
    }
}
