<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\DockerFactory;
use PhilippeVandermoere\DockerPhpSdk\DockerService;
use Http\Client\Curl\Client as CurlClient;
use Http\Client\HttpClient;

class DockerFactoryTest extends TestCase
{
    public function testDockerSocketPath(): void
    {
        static::assertEquals('/var/run/docker.sock', DockerFactory::getDockerSocketPath());
        DockerFactory::setDockerSocketPath(
            $dockerSocketPath = tempnam(sys_get_temp_dir(), 'docker')
        );

        static::assertEquals($dockerSocketPath, DockerFactory::getDockerSocketPath());
    }

    public function testCreateDockerClient(): void
    {
        $curlClient = DockerFactory::createDockerClient();
        static::assertInstanceOf(CurlClient::class, $curlClient);
        static::assertEquals(
            DockerFactory::getDockerSocketPath(),
            $this->getCurlOption($curlClient, CURLOPT_UNIX_SOCKET_PATH)
        );
    }

    public function testCreateDockerService(): void
    {
        static::assertInstanceOf(
            DockerService::class,
            DockerFactory::createDockerService($this->createMock(HttpClient::class))
        );
    }

    protected function getCurlOption(CurlClient $curlClient, int $option)
    {
        $reflectionClass = new \ReflectionClass(CurlClient::class);
        $reflectionProperty = $reflectionClass->getProperty('options');
        $reflectionProperty->setAccessible(true);

        $curlOptions = $reflectionProperty->getValue($curlClient);

        return $curlOptions[$option] ?? null;
    }
}
