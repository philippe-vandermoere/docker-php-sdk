<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkService;
use Http\Client\HttpClient;
use Psr\Http\Message\ResponseInterface;
use Faker\Factory as FakerFactory;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkCollection;
use PhilippeVandermoere\DockerPhpSdk\Network\Network;

class NetworkServiceTest extends TestCase
{
    public function testList(): void
    {
        $faker = FakerFactory::create();
        $networkService = $this
            ->getMockBuilder(NetworkService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $networkService
            ->method('sendRequest')
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $data = [];
        $networkCollection = new NetworkCollection();
        for ($i = 0; $i <= 50; $i++) {
            $stdClass = new \stdClass();
            $stdClass->Id = $faker->uuid;
            $stdClass->Name = $faker->text;
            $stdClass->Driver = $faker->text;
            $data[] = $stdClass;
            $networkCollection[] = new Network(
                $stdClass->Id,
                $stdClass->Name,
                $stdClass->Driver
            );
        }

        $networkService
            ->method('jsonDecodeResponse')
            ->willReturn($data)
        ;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with('/networks')
        ;

        $networkService
            ->expects($this->once())
            ->method('jsonDecodeResponse')
            ->with($response)
        ;

        static::assertEquals($networkCollection, $networkService->list());
    }

    public function testConnectContainer(): void
    {
        $faker = FakerFactory::create();
        $networkService = $this
            ->getMockBuilder(NetworkService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $networkService
            ->method('sendRequest')
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $networkId = $faker->uuid;
        $containerId = $faker->uuid;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                '/networks/' . $networkId . '/connect',
                'POST',
                ['Container' => $containerId]
            )
        ;

        static::assertEquals(
            $networkService,
            $networkService->connectContainer($networkId, $containerId)
        );
    }
}
