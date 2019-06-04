<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class LabelCollection extends ObjectArray
{
    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Label::class);
    }

    public function offsetGet($offset): Label
    {
        return parent::offsetGet($offset);
    }
}
