<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class ContainerCollection extends ObjectArray
{
    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Container::class);
    }

    public function offsetGet($offset): Container
    {
        return parent::offsetGet($offset);
    }
}
