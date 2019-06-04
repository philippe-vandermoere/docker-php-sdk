<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TarStream
{
    public const TAR_NO_COMPRESSION = '';

    public const TAR_COMPRESSION_GZIP = '-z';

    public const TAR_COMPRESSION_BZIP2 = '-j';

    public const TAR_COMPRESSION_XZ = '-J';

    /** @var string */
    protected $directory;

    /** @var string */
    protected $tarCompression;

    /** @var resource */
    protected $stream;

    /** @var ?string */
    protected $temporaryFilename;

    public function __construct(string $directory, string $tarCompression = self::TAR_NO_COMPRESSION)
    {
        if (false === \is_dir($directory)) {
            throw new \RuntimeException('The directory ' . $directory . ' doesn\'t exist.');
        }

        if (static::TAR_NO_COMPRESSION !== $tarCompression
            && static::TAR_COMPRESSION_GZIP !== $tarCompression
            && static::TAR_COMPRESSION_BZIP2 !== $tarCompression
            && static::TAR_COMPRESSION_XZ !== $tarCompression
        ) {
            throw new \RuntimeException('The tar compression format ' . $tarCompression . ' is not valid.');
        }

        $this->directory = rtrim($directory, '/');
        $this->tarCompression = $tarCompression;
    }

    public function getStream(string $excludeFile = null)
    {
        $this->closeStream();
        $temporaryFilename = \tempnam(\sys_get_temp_dir(), 'tar');

        if (false === $temporaryFilename) {
            throw new \RuntimeException('Unable to create temporary tar file.');
        }

        $this->temporaryFilename = $temporaryFilename;

        $cmd = ['tar', '-c', '-f', $this->temporaryFilename];
        if (static::TAR_NO_COMPRESSION !== $this->tarCompression) {
            $cmd[] = $this->tarCompression;
        }

        if (\is_file($this->directory . '/' . $excludeFile)) {
            $cmd[] = '-X';
            $cmd[] = $excludeFile;
        }

        $cmd[] = '.';

        $process = new Process(
            $cmd,
            $this->directory
        );

        if (0 !== $process->run()) {
            throw new ProcessFailedException($process);
        }

        $stream = \fopen($this->temporaryFilename, 'r');

        if (false === \is_resource($stream)) {
            throw new \RuntimeException('Unable to open temporary tar file.');
        }

        $this->stream = $stream;

        return $this->stream;
    }

    public function closeStream(): self
    {
        if (\is_resource($this->stream)) {
            \fclose($this->stream);
        }

        if (null !== $this->temporaryFilename && \is_writable($this->temporaryFilename)) {
            @\unlink($this->temporaryFilename);
            $this->temporaryFilename = null;
        }

        return $this;
    }

    public function __destruct()
    {
        $this->closeStream();
    }
}
