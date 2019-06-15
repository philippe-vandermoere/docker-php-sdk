<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Image;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Image\TarStream;
use Faker\Factory as FakerFactory;

class TarStreamTest extends TestCase
{
    public function testConstruct(): void
    {
        static::assertEquals('', TarStream::TAR_NO_COMPRESSION);
        static::assertEquals('z', TarStream::TAR_COMPRESSION_GZIP);
        static::assertEquals('j', TarStream::TAR_COMPRESSION_BZIP2);
        static::assertEquals('J', TarStream::TAR_COMPRESSION_XZ);

        $faker = FakerFactory::create();
        $excludeFile = $faker->word;
        file_put_contents(sys_get_temp_dir() . '/' . $excludeFile, '*');
        new TarStream(
            sys_get_temp_dir(),
            TarStream::TAR_COMPRESSION_GZIP,
            $excludeFile
        );

        @unlink($excludeFile);
    }

    public function testConstructErrorDirectory(): void
    {
        $faker = FakerFactory::create();
        $directory = sys_get_temp_dir() . '/' . __METHOD__ . $faker->word;

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('The directory ' . $directory . ' doesn\'t exist.');
        new TarStream($directory);
    }

    public function testConstructErrorTarCompression(): void
    {
        $faker = FakerFactory::create();
        $tarCompression = $faker->word;

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('The tar compression format ' . $tarCompression . ' is not valid.');
        new TarStream(sys_get_temp_dir(), $tarCompression);
    }

    public function testClose(): void
    {
        $tarStream = new TarStream(sys_get_temp_dir());
        static::assertEquals(
            true,
            is_resource(
                $this->getProtectedProperty(
                    $tarStream,
                    'process'
                )
            )
        );

        $tarStream->close();

        static::assertEquals(
            null,
            $this->getProtectedProperty(
                $tarStream,
                'process'
            )
        );
    }

    public function testGetSize(): void
    {
        $tarStream = new TarStream(sys_get_temp_dir());
        static::assertEquals(null, $tarStream->getSize());
    }

    protected function getProtectedProperty(TarStream $tarStream, string $property)
    {
        $reflectionClass = new \ReflectionClass($tarStream);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($tarStream);
    }

    protected function setProtectedProperty(TarStream $tarStream, string $property, $value): self
    {
        $reflectionClass = new \ReflectionClass($tarStream);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($tarStream, $value);

        return $this;
    }
}
