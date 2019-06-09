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
    protected static $dockerSocketPath = '/var/run/docker.sock';

    public static function setDockerSocketPath(string $dockerSocketPath): void
    {
        static::$dockerSocketPath = $dockerSocketPath;
    }

    public static function getDockerSocketPath(): string
    {
        return static::$dockerSocketPath;
    }

    public static function createDockerClient(): CurlClient
    {
        return new CurlClient(
            null,
            null,
            [CURLOPT_UNIX_SOCKET_PATH => static::$dockerSocketPath]
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
