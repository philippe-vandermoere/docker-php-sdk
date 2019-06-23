<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use GuzzleHttp\Psr7\Stream;

interface BuildContextInterface
{
    public function getRemote(): ?string;

    public function getStream(): ?Stream;
}
