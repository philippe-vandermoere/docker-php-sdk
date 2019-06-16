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

class ProcessCollection extends ObjectArray
{
    use ObjectArrayTrait;

    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Process::class);
    }

    public function offsetGet($offset): Process
    {
        return parent::offsetGet($offset);
    }

    public function current(): Process
    {
        return parent::current();
    }
}
