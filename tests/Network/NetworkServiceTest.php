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
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $networkService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
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
            ->method('jsonDecode')
            ->willReturn($data)
        ;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/networks'
            )
        ;

        $networkService
            ->expects($this->once())
            ->method('jsonDecode')
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
            ->willReturn($faker->text)
        ;

        $networkId = $faker->uuid;
        $containerId = $faker->uuid;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/networks/' . $networkId . '/connect',
                ['Content-Type' => 'application/json'],
                ['Container' => $containerId]
            )
        ;

        static::assertEquals(
            $networkService,
            $networkService->connectContainer($networkId, $containerId)
        );
    }

    public function testConnectContainerAliases(): void
    {
        $faker = FakerFactory::create();
        $networkService = $this
            ->getMockBuilder(NetworkService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $aliases = [];
        for ($i = 0; $i <= 25; $i++) {
            $aliases[] = $faker->domainName;
        }

        $networkService
            ->method('sendRequest')
            ->willReturn($faker->text)
        ;

        $networkId = $faker->uuid;
        $containerId = $faker->uuid;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/networks/' . $networkId . '/connect',
                ['Content-Type' => 'application/json'],
                [
                    'Container' => $containerId,
                    'EndpointConfig' => [
                        'Aliases' => $aliases
                    ]
                ]
            )
        ;

        static::assertEquals(
            $networkService,
            $networkService->connectContainer($networkId, $containerId, $aliases)
        );
    }

    public function testDisconnectContainer(): void
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
            ->willReturn($faker->text)
        ;

        $networkId = $faker->uuid;
        $containerId = $faker->uuid;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/networks/' . $networkId . '/disconnect',
                ['Content-Type' => 'application/json'],
                ['Container' => $containerId]
            )
        ;

        static::assertEquals(
            $networkService,
            $networkService->disconnectContainer($networkId, $containerId)
        );
    }
}
