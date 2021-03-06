<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use Http\Client\HttpClient;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\AbstractService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Faker\Factory as FakerFactory;
use Psr\Http\Message\StreamInterface;

class AbstractServiceTest extends TestCase
{
    public function testConstruct(): void
    {
        $dockerClient = $this->createMock(HttpClient::class);
        $abstractService = $this
            ->getMockBuilder(AbstractService::class)
            ->setConstructorArgs([$dockerClient])
            ->getMock()
        ;

        static::assertEquals($dockerClient, $this->getDockerClient($abstractService));
        static::assertEquals(1.37, $this->getDockerApiVersion($abstractService));
        static::assertEquals(true, method_exists($abstractService, 'jsonEncode'));
        static::assertEquals(true, method_exists($abstractService, 'jsonDecode'));
    }

    /** @dataProvider getDataRequest */
    public function testSendRequest(string $method, string $route, array $headers, $body, $expectedBody): void
    {
        $faker = FakerFactory::create();
        $dockerClient = $this->createMock(HttpClient::class);
        $abstractService = $this
            ->getMockBuilder(AbstractService::class)
            ->setConstructorArgs([$dockerClient])
            ->setMethods(['createRequest'])
            ->getMock()
        ;

        $responseInterface = $this->createMock(ResponseInterface::class);

        $dockerClient
            ->method('sendRequest')
            ->willReturn($responseInterface)
        ;

        $responseInterface
            ->method('getStatusCode')
            ->willReturn($statusCode = 200)
        ;

        $responseInterface
            ->method('getBody')
            ->willReturn($streamInterface = $this->createMock(StreamInterface::class))
        ;

        $streamInterface
            ->method('getContents')
            ->willReturn($response = $faker->text)
        ;

        $abstractService
            ->method('createRequest')
            ->willReturn($request = $this->createMock(RequestInterface::class))
        ;

        $abstractService
            ->expects($this->once())
            ->method('createRequest')
            ->with(
                $method,
                'http://' . $this->getDockerApiVersion($abstractService) . $route,
                $headers,
                $expectedBody
            )
        ;

        $dockerClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($request)
        ;

        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->with()
        ;

        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->with()
        ;

        $streamInterface
            ->expects($this->once())
            ->method('getContents')
            ->with()
        ;

        static::assertEquals(
            $response,
            $this->callProtectedMethod(
                $abstractService,
                'sendRequest',
                [$method, $route, $headers, $body]
            )
        );
    }

    public function testSendRequestError(): void
    {
        $faker = FakerFactory::create();
        $dockerClient = $this->createMock(HttpClient::class);
        $abstractService = $this
            ->getMockBuilder(AbstractService::class)
            ->setConstructorArgs([$dockerClient])
            ->getMock()
        ;

        $responseInterface = $this->createMock(ResponseInterface::class);

        $dockerClient
            ->method('sendRequest')
            ->willReturn($responseInterface)
        ;

        $responseInterface
            ->method('getStatusCode')
            ->willReturn($statusCode = mt_rand(400, 599))
        ;

        $responseInterface
            ->method('getReasonPhrase')
            ->willReturn($message = $faker->word)
        ;

        $request = new Request(
            'GET',
            'http://' . $this->getDockerApiVersion($abstractService) . $route = '/' . $faker->word,
            []
        );

        $dockerClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($request)
        ;

        $responseInterface
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->with()
        ;

        $responseInterface
            ->expects($this->once())
            ->method('getReasonPhrase')
            ->with()
        ;

        static::expectException(DockerException::class);
        static::expectExceptionMessage($message);
        static::expectExceptionCode($statusCode);
        $this->callProtectedMethod(
            $abstractService,
            'sendRequest',
            ['GET', $route]
        );
    }

    public function getDataRequest(): array
    {
        $faker = FakerFactory::create();
        return [
            ['GET', '/' . $faker->word, [], null, null],
            ['GET', '/' . $faker->word, [$faker->uuid => $faker->word], null, null],
            ['POST', '/' . $faker->word, [], null, null],
            ['POST', '/' . $faker->word, [], $body = $faker->word, $body],
            [
                'POST',
                '/' . $faker->word,
                ['Content-Type' => 'application/json'],
                $body = $faker->word,
                json_encode($body)
            ],
            [
                'POST', '/' . $faker->word,
                [],
                $body = new Stream(fopen(__FILE__, 'r')),
                $body
            ],
        ];
    }

    protected function getDockerClient(AbstractService $abstractService)
    {
        $reflectionClass = new \ReflectionClass($abstractService);
        $reflectionProperty = $reflectionClass->getProperty('dockerClient');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($abstractService);
    }

    protected function getDockerApiVersion(AbstractService $abstractService)
    {
        return (new \ReflectionClass($abstractService))->getConstant('DOCKER_API_VERSION');
    }

    protected function callProtectedMethod(AbstractService $abstractService, string $method, array $args = [])
    {
        $reflectionClass = new \ReflectionClass($abstractService);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($abstractService, $args);
    }
}
