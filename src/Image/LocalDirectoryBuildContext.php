<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use GuzzleHttp\Psr7\Stream;

class LocalDirectoryBuildContext implements BuildContextInterface
{
    /** @var string */
    protected $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function getRemote(): ?string
    {
        return null;
    }

    public function getStream(): ?Stream
    {
        return new TarStream($this->directory);
    }
}
