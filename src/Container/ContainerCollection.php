<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

use PhilippeVandermoere\DockerPhpSdk\ObjectArrayTrait;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class ContainerCollection extends ObjectArray
{
    use ObjectArrayTrait;

    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Container::class);
    }

    public function offsetGet($offset): Container
    {
        return parent::offsetGet($offset);
    }

    public function current(): Container
    {
        return parent::current();
    }

    public function has(string $containerId): bool
    {
        foreach ($this as $container) {
            if ($container->getId() === $containerId) {
                return true;
            }
        }

        return false;
    }
}
