<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkCreateOptions;
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
        for ($i = 0; $i <= 10; $i++) {
            $network = $this->createNetwork();
            $data[] = $this->createStdClass($network);
            $networkCollection[] = $network;
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

    public function testGet(): void
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

        $network = $this->createNetwork();
        $networkService
            ->method('jsonDecode')
            ->willReturn($this->createStdClass($network))
        ;

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/networks/' . $network->getId()
            )
        ;

        $networkService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals($network, $networkService->get($network->getId()));
    }

    public function testCreate(): void
    {
        $faker = FakerFactory::create();
        $networkService = $this
            ->getMockBuilder(NetworkService::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $networkService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;


        $networkCreateOptions = new NetworkCreateOptions();
        $network = $this->createNetwork(
            $networkCreateOptions->getDriver(),
            $networkCreateOptions->isInternal(),
            $networkCreateOptions->isAttachable(),
            $networkCreateOptions->getLabels()
        );

        $data = new \stdClass();
        $data->Id = $network->getId();

        $networkService
            ->method('jsonDecode')
            ->willReturn($data)
        ;

        $networkService
            ->method('get')
            ->willReturn($network)
        ;

        $body = [
            'Name' => $network->getName(),
            'Drivers' => $network->getDriver(),
            'Internal' => $network->isInternal(),
            'Attachable' => $network->isAttachable(),
        ];

        foreach ($network->getLabels() as $label) {
            $body['Labels'][$label->getName()] = $label->getValue();
        }

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/networks/create',
                ['Content-Type' => 'application/json'],
                $body
            )
        ;

        $networkService
            ->expects($this->once())
            ->method('get')
            ->with($network->getId())
        ;

        $networkService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals($network, $networkService->create($network->getName()));
    }

    public function testCreateWithOptions(): void
    {
        $faker = FakerFactory::create();
        $networkService = $this
            ->getMockBuilder(NetworkService::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $networkService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;


        $network = $this->createNetwork();
        $data = new \stdClass();
        $data->Id = $network->getId();

        $networkCreateOptions = new NetworkCreateOptions();
        $networkCreateOptions
            ->setDriver($network->getDriver())
            ->setInternal($network->isInternal())
            ->setAttachable($network->isAttachable())
            ->setLabels($network->getLabels())
        ;

        $networkService
            ->method('jsonDecode')
            ->willReturn($data)
        ;

        $networkService
            ->method('get')
            ->willReturn($network)
        ;

        $body = [
            'Name' => $network->getName(),
            'Drivers' => $networkCreateOptions->getDriver(),
            'Internal' => $networkCreateOptions->isInternal(),
            'Attachable' => $networkCreateOptions->isAttachable(),
        ];

        foreach ($networkCreateOptions->getLabels() as $label) {
            $body['Labels'][$label->getName()] = $label->getValue();
        }

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/networks/create',
                ['Content-Type' => 'application/json'],
                $body
            )
        ;

        $networkService
            ->expects($this->once())
            ->method('get')
            ->with($network->getId())
        ;

        $networkService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals(
            $network,
            $networkService->create(
                $network->getName(),
                $networkCreateOptions
            )
        );
    }

    public function testRemove(): void
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

        $networkService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'DELETE',
                '/networks/' . $networkId
            )
        ;

        static::assertEquals(
            $networkService,
            $networkService->remove($networkId)
        );
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

    protected function createNetwork(
        string $driver = null,
        bool $internal = null,
        bool $attachable = null,
        LabelCollection $labels = null
    ): Network {
        $faker = FakerFactory::create();

        if (false === $labels instanceof LabelCollection) {
            $labels = new LabelCollection();
            if (true === (bool) mt_rand(0, 1)) {
                for ($i = 0; $i <= 5; $i++) {
                    $labels[] = new Label($faker->text, $faker->text);
                }
            }
        }

        return new Network(
            $faker->uuid,
            $faker->text,
            $driver ?? $faker->text,
            $internal ?? (bool) mt_rand(0, 1),
            $attachable ?? (bool) mt_rand(0, 1),
            $labels
        );
    }

    protected function createStdClass(Network $network): \stdClass
    {
        $stdClass = new \stdClass();
        $stdClass->Id = $network->getId();
        $stdClass->Name = $network->getName();
        $stdClass->Driver = $network->getDriver();
        $stdClass->Internal = $network->isInternal();
        $stdClass->Attachable = $network->isAttachable();

        if (0 !== $network->getLabels()->count()) {
            $stdClass->Labels = [];
            foreach ($network->getLabels() as $label) {
                $stdClass->Labels[$label->getName()] = $label->getValue();
            }
            $network->getLabels()->rewind();
        }

        return $stdClass;
    }
}
