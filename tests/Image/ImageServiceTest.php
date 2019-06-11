<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Image;

use Http\Client\HttpClient;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerBuildException;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerInvalidFormatException;
use PhilippeVandermoere\DockerPhpSdk\Image\DockerAuthentication;
use PhilippeVandermoere\DockerPhpSdk\Image\TarStream;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Image\ImageService;
use Faker\Factory as FakerFactory;

class ImageServiceTest extends TestCase
{
    /** @dataProvider getDataBuild */
    public function testBuild(string $imageId, array $buildArgs, string $dockerfilePath): void
    {
        $query = [
            'dockerfile' => $dockerfilePath,
            'q' => true,
        ];

        if (0 < count($buildArgs)) {
            $query['buildargs'] = json_encode($buildArgs);
        }

        $imageService = $this
            ->getMockBuilder(ImageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $imageService
            ->method('sendRequest')
            ->willReturn($response = json_encode(['stream' => 'sha256:' . $imageId]));
        ;

        $tarStream = $this->createMock(TarStream::class);
        $tarStream
            ->method('getStream')
            ->willReturn($resource = fopen(__FILE__, 'r'))
        ;

        $imageService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/build?' . http_build_query($query),
                ['Content-Type' => 'application/x-tar'],
                $resource
            )
        ;

        $tarStream
            ->expects($this->once())
            ->method('getStream')
            ->with('.dockerignore')
        ;

        static::assertEquals(
            $imageId,
            $imageService->build($tarStream, $buildArgs, $dockerfilePath)
        );
    }

    public function testBuildErrorNoImageId(): void
    {
        $faker = FakerFactory::create();
        $imageService = $this
            ->getMockBuilder(ImageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $imageService
            ->method('sendRequest')
            ->willReturn($response = json_encode(['stream' => $faker->text]));
        ;

        $tarStream = $this->createMock(TarStream::class);
        $tarStream
            ->method('getStream')
            ->willReturn($resource = fopen(__FILE__, 'r'))
        ;

        $imageService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/build?' . http_build_query(['dockerfile' => 'Dockerfile', 'q' => true]),
                ['Content-Type' => 'application/x-tar'],
                $resource
            )
        ;

        $tarStream
            ->expects($this->once())
            ->method('getStream')
            ->with('.dockerignore')
        ;

        static::expectException(DockerBuildException::class);
        static::expectExceptionMessage('Unable to get image Id.');

        $imageService->build($tarStream);
    }

    public function testBuildErrorBuild(): void
    {
        $faker = FakerFactory::create();
        $imageService = $this
            ->getMockBuilder(ImageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $error = [
            'errorDetail' => [
                'message' => $errorMessage = $faker->text,
                'code' => $errorCode = mt_rand(1, 255),
            ],
        ];

        $imageService
            ->method('sendRequest')
            ->willReturn($response = json_encode($error));
        ;

        $tarStream = $this->createMock(TarStream::class);
        $tarStream
            ->method('getStream')
            ->willReturn($resource = fopen(__FILE__, 'r'))
        ;

        $imageService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/build?' . http_build_query(['dockerfile' => 'Dockerfile', 'q' => true]),
                ['Content-Type' => 'application/x-tar'],
                $resource
            )
        ;

        $tarStream
            ->expects($this->once())
            ->method('getStream')
            ->with('.dockerignore')
        ;

        static::expectException(DockerBuildException::class);
        static::expectExceptionMessage($errorMessage);
        static::expectExceptionCode($errorCode);

        $imageService->build($tarStream);
    }

    public function testTag(): void
    {
        $faker = FakerFactory::create();
        $imageService = $this
            ->getMockBuilder(ImageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $imageService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;

        $imageId = $faker->uuid;
        $repository = $faker->word;
        $tag = $faker->word;

        $imageService
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                'POST',
                '/images/' . $imageId . '/tag?' . \http_build_query(
                    [
                        'repo' => $repository,
                        'tag' => $tag,
                    ]
                )
            )
        ;

        static::assertEquals(
            $imageService,
            $imageService->tag($imageId, $repository, $tag)
        );
    }

    /** @dataProvider getInvalidRepository */
    public function testTagInvalidRepository(string $repository): void
    {
        $faker = FakerFactory::create();
        static::expectException(DockerInvalidFormatException::class);
        static::expectExceptionMessage('Parameter `' . $repository . '` must respect format `[a-z0-9.\/\_-]+`.');

        $imageService = new ImageService($this->createMock(HttpClient::class));
        $imageService->tag(
            $faker->uuid,
            $repository,
            $faker->word
        );
    }

    /** @dataProvider getInvalidTag */
    public function testTagInvalidTag(string $tag): void
    {
        $faker = FakerFactory::create();
        static::expectException(DockerInvalidFormatException::class);
        static::expectExceptionMessage('Parameter `' . $tag . '` must respect format `[a-z0-9.\_-]+`.');

        $imageService = new ImageService($this->createMock(HttpClient::class));
        $imageService->tag(
            $faker->uuid,
            $faker->word,
            $tag
        );
    }

    public function testPush(): void
    {
        $faker = FakerFactory::create();
        $imageService = $this
            ->getMockBuilder(ImageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock()
        ;

        $imageService
            ->method('sendRequest')
            ->willReturn($response = $faker->text)
        ;

        $imageId = $faker->uuid;
        $dockerAuthentication = $this->createMock(DockerAuthentication::class);
        $repository = $faker->word;
        $tag = $faker->word;

        $dockerAuthentication
            ->method('getRegistry')
            ->willReturn($registry = $faker->word)
        ;

        $dockerAuthentication
            ->method('getDockerCredential')
            ->willReturn($auth = $faker->word)
        ;

        $imageService
            ->expects($this->exactly(2))
            ->method('sendRequest')
            ->withConsecutive(
                [
                    'POST',
                    '/images/' . $imageId . '/tag?' . \http_build_query(
                        [
                            'repo' => $registry . '/' .$repository,
                            'tag' => $tag,
                        ]
                    )
                ],
                [
                    'POST',
                    '/images/' . $registry . '/' .$repository . ':' . $tag . '/push',
                    ['X-Registry-Auth' => $auth]
                ]
            )
        ;

        $dockerAuthentication
            ->expects($this->exactly(2))
            ->method('getRegistry')
            ->with()
        ;

        $dockerAuthentication
            ->expects($this->once())
            ->method('getDockerCredential')
            ->with()
        ;

        static::assertEquals(
            $imageService,
            $imageService->push($dockerAuthentication, $imageId, $repository, $tag)
        );
    }

    public function getDataBuild(): array
    {
        $faker = FakerFactory::create();
        return [
            [$faker->uuid, [], 'Dockerfile'],
            [$faker->uuid, [$faker->uuid => $faker->word, $faker->uuid => $faker->word], 'Dockerfile'],
            [$faker->uuid, [], $faker->word],
            [$faker->uuid, [$faker->uuid => $faker->word, $faker->uuid => $faker->word], $faker->word],
        ];
    }

    public function getInvalidRepository(): array
    {
        return [
            ['T'],
            ['é'],
            [' '],
            ["\t"],
            ["\n"],
            ["\r"],
        ];
    }

    public function getInvalidTag(): array
    {
        return [
            ['T'],
            ['/'],
            ['é'],
            [' '],
            ["\t"],
            ["\n"],
            ["\r"],
        ];
    }
}
