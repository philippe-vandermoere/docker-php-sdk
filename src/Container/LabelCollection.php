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

class LabelCollection extends ObjectArray
{
    use ObjectArrayTrait;

    public function __construct(iterable $values = [])
    {
        parent::__construct($values, Label::class);
    }

    public function offsetGet($offset): Label
    {
        return parent::offsetGet($offset);
    }

    public function current(): Label
    {
        return parent::current();
    }

    public function has(string $label): bool
    {
        foreach ($this as $currentLabel) {
            if ($currentLabel->getName() === $label) {
                return true;
            }
        }

        return false;
    }

    public function getValue(string $label): ?string
    {
        foreach ($this as $currentLabel) {
            if ($currentLabel->getName() === $label) {
                return $currentLabel->getValue();
            }
        }

        return null;
    }
}
