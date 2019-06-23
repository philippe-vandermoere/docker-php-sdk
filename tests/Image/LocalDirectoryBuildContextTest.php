<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Image;

use PhilippeVandermoere\DockerPhpSdk\Image\TarStream;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Image\LocalDirectoryBuildContext;

class LocalDirectoryBuildContextTest extends TestCase
{
    public function testGetRemote(): void
    {
        $localDirectoryBuildContext = new LocalDirectoryBuildContext(sys_get_temp_dir());
        static::assertEquals(null, $localDirectoryBuildContext->getRemote());
    }

    public function testGetStream(): void
    {
        $localDirectoryBuildContext = new LocalDirectoryBuildContext(sys_get_temp_dir());
        static::assertInstanceOf(TarStream::class, $localDirectoryBuildContext->getStream());
    }
}
