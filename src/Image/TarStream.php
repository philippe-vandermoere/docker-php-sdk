<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use GuzzleHttp\Psr7\Stream;
use Symfony\Component\Finder\Finder;

class TarStream extends Stream
{
    public const TAR_NO_COMPRESSION = '';
    public const TAR_COMPRESSION_GZIP = 'z';
    public const TAR_COMPRESSION_BZIP2 = 'j';
    public const TAR_COMPRESSION_XZ = 'J';

    /** @var ?resource */
    protected $process;

    public function __construct(
        string $directory,
        string $tarCompression = self::TAR_NO_COMPRESSION,
        string $excludeFile = null
    ) {
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

        $cmd = 'tar -c' . $tarCompression;
        if (\is_file($directory . '/' . $excludeFile)) {
            $cmd .= ' -X' . $excludeFile;
        }

        $cmd .= ' .';
        $process = \proc_open(
            $cmd,
            [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w'],
            ],
            $pipes,
            $directory
        );

        // @codeCoverageIgnoreStart
        if (false === \is_resource($process)) {
            throw new \RuntimeException('Unable to execute ' . $cmd . '.');
        }
        // @codeCoverageIgnoreEnd

        usleep(10000);
        // @codeCoverageIgnoreStart
        if (0 < \proc_get_status($process)['exitcode']) {
            $stdOut = \stream_get_contents($pipes[1]);
            $stdErr = \stream_get_contents($pipes[2]);
            $this->close();

            throw new \RuntimeException(
                'Error executing command: ' . $cmd . PHP_EOL
                . 'Output:' . $stdOut . PHP_EOL .
                'Error output:' . $stdErr
            );
        }
        // @codeCoverageIgnoreEnd

        $this->process = $process;

        parent::__construct($pipes[1], []);
    }

    public function close()
    {
        if (\is_resource($this->process)) {
            \proc_close($this->process);
            $this->process = null;
        }

        parent::close();
    }

    public function getSize()
    {
        return null;
    }
}
