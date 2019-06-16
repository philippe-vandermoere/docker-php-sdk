<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk;

trait ObjectArrayTrait
{
    public function filter(callable $filter): self
    {
        return new static(\array_filter($this->toArray(), $filter));
    }
}
