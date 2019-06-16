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

class NetworkCollection extends ObjectArray
{
    use ObjectArrayTrait;

    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Network::class);
    }

    public function offsetGet($offset): Network
    {
        return parent::offsetGet($offset);
    }

    public function current(): Network
    {
        return parent::current();
    }

    public function has(string $networkId): bool
    {
        foreach ($this as $network) {
            if ($networkId === $network->getId()) {
                return true;
            }
        }

        return false;
    }
}
