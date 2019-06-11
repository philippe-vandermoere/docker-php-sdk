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
use PhilippeVandermoere\DockerPhpSdk\Container\Network;
use PhilippeVandermoere\DockerPhpSdk\Container\NetworkCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Process;
use PhilippeVandermoere\DockerPhpSdk\Container\ProcessCollection;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerService;
use Faker\Factory as FakerFactory;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Container;

class ContainerServiceTest extends TestCase
{
    public function testList(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;

        $data = [];
        $containerCollection = new ContainerCollection();
        for ($i = 0; $i <= 25; $i++) {
            $container = $this->createContainer();
            $containerCollection[] = $container;
            $data[] = $this->getContainerStdClass($container);
        }

        $containerService
            ->method('jsonDecode')
            ->willReturn($data)
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/containers/json'
            )
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals($containerCollection, $containerService->list());
    }

    public function testGet(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;

        $container = $this->createContainer();

        $containerService
            ->method('jsonDecode')
            ->willReturn($this->getContainerStdClass($container))
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/containers/' . $container->getId() . '/json'
            )
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals($container, $containerService->get($container->getId()));
    }

    public function testGetProcess(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;

        $data = new \stdClass();
        $data->Processes = [];
        $processCollection = new ProcessCollection();
        for ($i = 0; $i <= 25; $i++) {
            $data->Processes[] = [
                0 => $uid = mt_rand(0, 999),
                1 => $pid = mt_rand(1, 65535),
                2 => $ppid = mt_rand(1, 65535),
                4 => $startTime = $faker->monthName . $faker->dayOfMonth,
                5 => $tty = '?',
                6 => $time = $faker->time(),
                7 => $cmd = $faker->text
            ];

            $processCollection[] = new Process(
                $uid,
                $pid,
                $ppid,
                $startTime,
                $tty,
                new \DateTimeImmutable($time),
                $cmd
            );
        }

        $containerService
            ->method('jsonDecode')
            ->willReturn($data)
        ;

        $containerId = $faker->uuid;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/containers/' . $containerId . '/top'
            )
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecode')
            ->with($response)
        ;

        static::assertEquals($processCollection, $containerService->getProcess($containerId));
    }

    /** @dataProvider getLogsData */
    public function testGetLogs(
        string $containerId,
        bool $stdout,
        bool $stderr,
        ?\DateTimeInterface $since,
        ?\DateTimeInterface $until,
        string $output
    ): void {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($output)
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'GET',
                '/containers/' . $containerId . '/logs?' . http_build_query(
                    [
                        'stdout' => $stdout,
                        'stderr' => $stderr,
                        'since' => ($since instanceof \DateTimeInterface) ? $since->getTimestamp() : 0,
                        'until' => ($until instanceof \DateTimeInterface) ? $until->getTimestamp() : 0,
                    ]
                )
            )
        ;

        static::assertEquals(
            trim($output),
            $containerService->getLogs($containerId, $stdout, $stderr, $since, $until)
        );
    }

    public function testStart(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($faker->word)
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/containers/' . $containerId . '/start'
            )
        ;

        static::assertEquals($containerService, $containerService->start($containerId));
    }

    public function testStop(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($faker->word)
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/containers/' . $containerId . '/stop'
            )
        ;

        static::assertEquals($containerService, $containerService->stop($containerId));
    }

    public function testRestart(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($faker->word)
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/containers/' . $containerId . '/restart'
            )
        ;

        static::assertEquals($containerService, $containerService->restart($containerId));
    }

    public function testExecuteCommand(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                $response1 = $faker->word,
                $response2 = $faker->word,
                $response3 = $faker->word
            )
        ;

        $stdClassId = new \stdClass();
        $stdClassId->Id = $id = $faker->uuid;

        $stdClassExitCode = new \stdClass();
        $stdClassExitCode->ExitCode = $exitCode = 0;

        $containerService
            ->method('jsonDecode')
            ->willReturnOnConsecutiveCalls(
                $stdClassId,
                $stdClassExitCode
            )
        ;

        $containerId = $faker->uuid;
        $command = [
            $faker->word,
            $faker->word,
            $faker->word
        ];

        if (true === (bool) mt_rand(0, 1)) {
            $workingDirectory = '/tmp/' . $faker->word;
        } else {
            $workingDirectory = null;
        }

        $containerService
            ->expects($this->exactly(3))
            ->method('sendRequest')
            ->withConsecutive(
                [
                    'POST',
                    '/containers/' . $containerId . '/exec',
                    ['Content-Type' => 'application/json'],
                    [
                        'AttachStdin' => false,
                        'AttachStdout' => true,
                        'AttachStderr' => true,
                        'Tty' => true,
                        'Cmd' => $command,
                        'WorkingDir' => $workingDirectory
                    ],
                ],
                [
                    'POST',
                    '/exec/' . $id . '/start',
                    ['Content-Type' => 'application/json'],
                    ['Detach' => false, 'Tty' => true],
                ],
                [
                    'GET',
                    '/exec/' . $id . '/json',
                ]
            )
        ;

        $containerService
            ->expects($this->exactly(2))
            ->method('jsonDecode')
            ->withConsecutive(
                [$response1],
                [$response3]
            )
        ;

        static::assertEquals(
            $response2,
            $containerService->executeCommand($containerId, $command, $workingDirectory)
        );
    }

    public function testExecuteCommandError(): void
    {
        $faker = FakerFactory::create();
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecode', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                $response1 = $faker->word,
                $response2 = $faker->word,
                $response3 = $faker->word
            )
        ;

        $stdClassId = new \stdClass();
        $stdClassId->Id = $id = $faker->uuid;

        $stdClassExitCode = new \stdClass();
        $stdClassExitCode->ExitCode = $exitCode = mt_rand(1, 255);

        $containerService
            ->method('jsonDecode')
            ->willReturnOnConsecutiveCalls(
                $stdClassId,
                $stdClassExitCode
            )
        ;

        $containerId = $faker->uuid;
        $command = [
            $faker->word,
            $faker->word,
            $faker->word
        ];

        if (true === (bool) mt_rand(0, 1)) {
            $workingDirectory = '/tmp/' . $faker->word;
        } else {
            $workingDirectory = null;
        }

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage($response2);
        static::expectExceptionCode($exitCode);

        $containerService->executeCommand($containerId, $command, $workingDirectory);
    }

    public function getLogsData(): array
    {
        $faker = FakerFactory::create();

        return [
            [$faker->uuid, true, true, null, null, $faker->word],
            [$faker->uuid, false, true, null, null, $faker->word],
            [$faker->uuid, true, false, null, null, $faker->word],
            [$faker->uuid, false, false, null, null, $faker->word],
            [$faker->uuid, true, true, $faker->dateTime, null, $faker->word],
            [$faker->uuid, false, true, $faker->dateTime, null, $faker->word],
            [$faker->uuid, true, false, $faker->dateTime, null, $faker->word],
            [$faker->uuid, false, false, $faker->dateTime, null, $faker->word],
            [$faker->uuid, true, true, null, $faker->dateTime, $faker->word],
            [$faker->uuid, false, true, null, $faker->dateTime, $faker->word],
            [$faker->uuid, true, false, null, $faker->dateTime, $faker->word],
            [$faker->uuid, false, false, null, $faker->dateTime, $faker->word],
            [$faker->uuid, true, true, $faker->dateTime, $faker->dateTime, $faker->word],
            [$faker->uuid, false, true, $faker->dateTime, $faker->dateTime, $faker->word],
            [$faker->uuid, true, false, $faker->dateTime, $faker->dateTime, $faker->word],
            [$faker->uuid, false, false, $faker->dateTime, $faker->dateTime, $faker->word],
        ];
    }

    protected function createContainer(): Container
    {
        $faker = FakerFactory::create();
        $container = new Container(
            $faker->uuid,
            $faker->text,
            $faker->text
        );

        if (true === (bool) mt_rand(0, 1)) {
            $networkCollection = new NetworkCollection();
            for ($i = 0; $i <= 5; $i++) {
                $networkCollection[] = new Network($faker->uuid, $faker->text, $faker->localIpv4);
            }

            $container->setNetworks($networkCollection);
        }

        if (true === (bool) mt_rand(0, 1)) {
            $labelCollection = new LabelCollection();
            for ($i = 0; $i <= 5; $i++) {
                $labelCollection[] = new Label($faker->text, $faker->localIpv4);
            }

            $container->setLabels($labelCollection);
        }

        return $container;
    }

    protected function getContainerStdClass(Container $container): \stdClass
    {
        $stdClass = new \stdClass();
        $stdClass->Id = $container->getId();
        if (true === (bool) mt_rand(0, 1)) {
            $stdClass->Name = '/' . $container->getName();
        } else {
            $stdClass->Names = [0 => '/' . $container->getName()];
        }

        $stdClass->Image = $container->getImage();

        if (0 !== $container->getNetworks()->count()) {
            $stdClass->NetworkSettings = new \stdClass();
            $stdClass->NetworkSettings->Networks = [];
            foreach ($container->getNetworks() as $network) {
                $networkStdClass = new \stdClass();
                $networkStdClass->NetworkID = $network->getId();
                $networkStdClass->IPAddress = $network->getIp();
                $stdClass->NetworkSettings->Networks[$network->getName()] = $networkStdClass;
            }

            $container->getNetworks()->rewind();
        }

        if (0 !== $container->getLabels()->count()) {
            $stdClass->Labels = [];
            foreach ($container->getLabels() as $label) {
                $stdClass->Labels[$label->getName()] = $label->getValue();
            }

            $container->getLabels()->rewind();
        }

        return $stdClass;
    }
}