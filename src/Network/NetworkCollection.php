<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\ObjectArrayTrait;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class NetworkCollection extends ObjectArray
{
    use ObjectArrayTrait;

    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Network::class);
    }

    public function offsetSet($offset, $value): void
    {
        /** @var Network $value */
        parent::offsetSet($value->getId(), $value);
    }

    public function offsetGet($offset): Network
    {
        return parent::offsetGet($offset);
    }

    public function current(): Network
    {
        return parent::current();
    }
}
