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
use Psr\Http\Message\ResponseInterface;
use Faker\Factory as FakerFactory;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Container;
use Psr\Http\Message\StreamInterface;

class ContainerServiceTest extends TestCase
{
    public function testList(): void
    {
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $data = [];
        $containerCollection = new ContainerCollection();
        for ($i = 0; $i <= 25; $i++) {
            $container = $this->createContainer();
            $containerCollection[] = $container;
            $data[] = $this->getContainerStdClass($container);
        }

        $containerService
            ->method('jsonDecodeResponse')
            ->willReturn($data)
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with('/containers/json')
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecodeResponse')
            ->with($response)
        ;

        static::assertEquals($containerCollection, $containerService->list());
    }

    public function testGet(): void
    {
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $container = $this->createContainer();

        $containerService
            ->method('jsonDecodeResponse')
            ->willReturn($this->getContainerStdClass($container))
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with('/containers/' . $container->getId() . '/json')
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecodeResponse')
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
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($response = $this->createMock(ResponseInterface::class))
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
            ->method('jsonDecodeResponse')
            ->willReturn($data)
        ;

        $containerId = $faker->uuid;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with('/containers/' . $containerId . '/top')
        ;

        $containerService
            ->expects($this->once())
            ->method('jsonDecodeResponse')
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
        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturn($responseInterface = $this->createMock(ResponseInterface::class))
        ;

        $responseInterface
            ->method('getBody')
            ->willReturn($streamInterface = $this->createMock(StreamInterface::class))
        ;

        $streamInterface
            ->method('getContents')
            ->willReturn($output)
        ;

        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
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
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                '/containers/' . $containerId . '/start',
                'POST'
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
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                '/containers/' . $containerId . '/stop',
                'POST'
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
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $containerId = $faker->uuid;
        $containerService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                '/containers/' . $containerId . '/restart',
                'POST'
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
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                $response1 = $this->createMock(ResponseInterface::class),
                $response2 = $this->createMock(ResponseInterface::class),
                $response3 = $this->createMock(ResponseInterface::class)
            )
        ;

        $stdClassId = new \stdClass();
        $stdClassId->Id = $id = $faker->uuid;

        $stdClassExitCode = new \stdClass();
        $stdClassExitCode->ExitCode = $exitCode = 0;

        $containerService
            ->method('jsonDecodeResponse')
            ->willReturnOnConsecutiveCalls(
                $stdClassId,
                $stdClassExitCode
            )
        ;

        $response2
            ->method('getBody')
            ->willReturn($streamInterface = $this->createMock(StreamInterface::class))
        ;

        $streamInterface
            ->method('getContents')
            ->willReturn($output = $faker->text)
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
                    '/containers/' . $containerId . '/exec',
                    'POST',
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
                    '/exec/' . $id . '/start',
                    'POST',
                    ['Detach' => false, 'Tty' => true],
                ],
                [
                    '/exec/' . $id . '/json',
                ]
            )
        ;

        $containerService
            ->expects($this->exactly(2))
            ->method('jsonDecodeResponse')
            ->withConsecutive(
                [$response1],
                [$response3]
            )
        ;

        static::assertEquals(
            $output,
            $containerService->executeCommand($containerId, $command, $workingDirectory)
        );
    }

    public function testExecuteCommandError(): void
    {
        $faker = FakerFactory::create();

        $containerService = $this
            ->getMockBuilder(ContainerService::class)
            ->disableOriginalConstructor()
            ->setMethods(['jsonDecodeResponse', 'sendRequest'])
            ->getMock()
        ;

        $containerService
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                $response1 = $this->createMock(ResponseInterface::class),
                $response2 = $this->createMock(ResponseInterface::class),
                $response3 = $this->createMock(ResponseInterface::class)
            )
        ;

        $stdClassId = new \stdClass();
        $stdClassId->Id = $id = $faker->uuid;

        $stdClassExitCode = new \stdClass();
        $stdClassExitCode->ExitCode = $exitCode = mt_rand(1, 255);

        $containerService
            ->method('jsonDecodeResponse')
            ->willReturnOnConsecutiveCalls(
                $stdClassId,
                $stdClassExitCode
            )
        ;

        $response2
            ->method('getBody')
            ->willReturn($streamInterface = $this->createMock(StreamInterface::class))
        ;

        $streamInterface
            ->method('getContents')
            ->willReturn($output = $faker->text)
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
        static::expectExceptionMessage($output);
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
