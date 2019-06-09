<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\DockerService;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerService;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkService;
use PhilippeVandermoere\DockerPhpSdk\Image\ImageService;

class DockerServiceTest extends TestCase
{
    public function testConstruct(): void
    {
        $dockerService = new DockerService(
            $containerService = $this->createMock(ContainerService::class),
            $networkService = $this->createMock(NetworkService::class),
            $imageService = $this->createMock(ImageService::class)
        );

        static::assertEquals($containerService, $dockerService->getContainerService());
        static::assertEquals($networkService, $dockerService->getNetworkService());
        static::assertEquals($imageService, $dockerService->getImageService());
    }
}
