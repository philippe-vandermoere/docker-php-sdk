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
use Faker\Factory as FakerFactory;

class DockerFactoryTest extends TestCase
{
    public function testConstants(): void
    {
        static::assertEquals('/var/run/docker.sock', DockerFactory::DOCKER_SOCKET_PATH);
        static::assertEquals('127.0.0.1', DockerFactory::DOCKER_TCP_HOST);
        static::assertEquals(2375, DockerFactory::DOCKER_TCP_PORT);
    }

    public function testCreateSocketDockerClient(): void
    {
        $faker = FakerFactory::create();
        $curlClient = DockerFactory::createSocketDockerClient($socket = $faker->word);
        static::assertInstanceOf(CurlClient::class, $curlClient);
        static::assertEquals(
            $socket,
            $this->getCurlOption($curlClient, CURLOPT_UNIX_SOCKET_PATH)
        );
    }

    public function testCreateTCPDockerClient(): void
    {
        $faker = FakerFactory::create();
        $curlClient = DockerFactory::createTCPDockerClient(
            $host = $faker->localIpv4 . ':' . mt_rand(1, 65535)
        );
        static::assertInstanceOf(CurlClient::class, $curlClient);
        static::assertEquals(
            $host,
            $this->getCurlOption($curlClient, CURLOPT_PROXY)
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
