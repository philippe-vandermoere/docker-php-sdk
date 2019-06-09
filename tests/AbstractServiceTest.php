<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;
use PhilippeVandermoere\DockerPhpSdk\Exception\JsonException;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\AbstractService;
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
    }

    public function testSendRequest(): void
    {
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
            ->willReturn(200)
        ;

        $method = 'POST';
        $route = '/toto';
        $data = [];

        if (count($data) > 0) {
            $headers = ['Content-Type' => 'application/json'];
            $body = \json_encode($data);
        } else {
            $headers = [];
            $body = null;
        }

        $request = new Request(
            $method,
            'http://1.37' . $route,
            $headers,
            $body
        );

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

        static::assertEquals(
            $responseInterface,
            $this->callProtectedMethod(
                $abstractService,
                'sendRequest',
                [$route, $method, $data]
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
            'http://1.37/' . $route = $faker->word,
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
            ['/' . $route]
        );
    }

    public function testJsonEncodeValidJson(): void
    {
        $faker = FakerFactory::create();
        $abstractService = $this->createMock(AbstractService::class);

        $data = [
            $faker->word,
            mt_rand(0, PHP_INT_MAX),
            new \stdClass(),
        ];

        $json = \json_encode($data);

        static::assertEquals(
            $json,
            $this->callProtectedMethod(
                $abstractService,
                'jsonEncode',
                [$data]
            )
        );
    }

    public function testJsonEncodeInvalidJson(): void
    {
        $abstractService = $this->createMock(AbstractService::class);
        static::expectException(JsonException::class);
        $this->callProtectedMethod(
            $abstractService,
            'jsonEncode',
            [[\chr(200)]]
        );
    }

    public function testJsonDecodeValidJson(): void
    {
        $faker = FakerFactory::create();
        $abstractService = $this->createMock(AbstractService::class);

        $json = \json_encode([
            'Id' => $id = $faker->uuid,
            'Name' => $name = $faker->word,
        ]);

        $data = new \stdClass();
        $data->Id = $id;
        $data->Name = $name;

        static::assertEquals(
            $data,
            $this->callProtectedMethod(
                $abstractService,
                'jsonDecode',
                [$json]
            )
        );
    }

    public function testJsonDecodeInvalidJson(): void
    {
        $abstractService = $this->createMock(AbstractService::class);
        static::expectException(JsonException::class);
        $this->callProtectedMethod(
            $abstractService,
            'jsonDecode',
            ['{aaaa']
        );
    }

    public function testJsonDecodeResponse(): void
    {
        $faker = FakerFactory::create();
        $abstractService = $this
            ->getMockBuilder(AbstractService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode'])
            ->getMock()
        ;

        $responseInterface = $this->createMock(ResponseInterface::class);

        $abstractService
            ->method('jsonDecode')
            ->willReturn($data = new \stdClass())
        ;

        $responseInterface
            ->method('getBody')
            ->willReturn($streamInterface = $this->createMock(StreamInterface::class))
        ;

        $streamInterface
            ->method('getContents')
            ->willReturn($json = json_encode($faker->word))
        ;

        $abstractService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($json)
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
            $data,
            $this->callProtectedMethod(
                $abstractService,
                'jsonDecodeResponse',
                [$responseInterface]
            )
        );
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
