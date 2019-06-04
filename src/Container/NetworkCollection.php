<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class NetworkCollection extends ObjectArray
{
    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Network::class);
    }

    public function offsetGet($offset): Network
    {
        return parent::offsetGet($offset);
    }
}
