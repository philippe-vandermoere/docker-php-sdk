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
        static::assertEquals('-z', TarStream::TAR_COMPRESSION_GZIP);
        static::assertEquals('-j', TarStream::TAR_COMPRESSION_BZIP2);
        static::assertEquals('-J', TarStream::TAR_COMPRESSION_XZ);

        $directory = sys_get_temp_dir();
        static::assertEquals(
            $directory,
            $this->getProtectedProperty(new TarStream($directory), 'directory')
        );
        static::assertEquals(
            $directory,
            $this->getProtectedProperty(new TarStream($directory . '/'), 'directory')
        );

        static::assertEquals(
            TarStream::TAR_NO_COMPRESSION,
            $this->getProtectedProperty(
                new TarStream($directory),
                'tarCompression'
            )
        );

        static::assertEquals(
            TarStream::TAR_NO_COMPRESSION,
            $this->getProtectedProperty(
                new TarStream($directory, TarStream::TAR_NO_COMPRESSION),
                'tarCompression'
            )
        );

        static::assertEquals(
            TarStream::TAR_COMPRESSION_GZIP,
            $this->getProtectedProperty(
                new TarStream($directory, TarStream::TAR_COMPRESSION_GZIP),
                'tarCompression'
            )
        );

        static::assertEquals(
            TarStream::TAR_COMPRESSION_BZIP2,
            $this->getProtectedProperty(
                new TarStream($directory, TarStream::TAR_COMPRESSION_BZIP2),
                'tarCompression'
            )
        );

        static::assertEquals(
            TarStream::TAR_COMPRESSION_XZ,
            $this->getProtectedProperty(
                new TarStream($directory, TarStream::TAR_COMPRESSION_XZ),
                'tarCompression'
            )
        );
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

    public function testGetStream(): void
    {
        $faker = FakerFactory::create();
        $directory = sys_get_temp_dir() . '/' . __METHOD__ .  $faker->word;
        @unlink($directory);
        mkdir($directory);
        $tarStream = new TarStream($directory, TarStream::TAR_COMPRESSION_GZIP);

        static::assertEquals(
            true,
            is_resource($tarStream->getStream())
        );

        file_put_contents($directory . '/exclude', '');
        static::assertEquals(
            true,
            is_resource($tarStream->getStream('exclude'))
        );
    }

    public function testCloseStream(): void
    {
        $tarStream = new TarStream(sys_get_temp_dir());
        static::assertEquals($tarStream, $tarStream->closeStream());
        static::assertEquals(
            false,
            is_resource($this->getProtectedProperty($tarStream, 'stream'))
        );
        static::assertEquals(
            null,
            $this->getProtectedProperty($tarStream, 'temporaryFilename')
        );

        $this
            ->setProtectedProperty(
                $tarStream,
                'stream',
                $handle = fopen(__FILE__, 'r')
            )
            ->setProtectedProperty(
                $tarStream,
                'temporaryFilename',
                $tmpFile = tempnam(sys_get_temp_dir(), 'test')
            )
        ;

        $tarStream->__destruct();
        static::assertEquals(
            false,
            is_resource($handle)
        );

        static::assertEquals(
            false,
            file_exists($tmpFile)
        );
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
