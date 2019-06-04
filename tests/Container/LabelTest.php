<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use Faker\Factory as FakerFactory;

class LabelTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $label = new Label($name = $faker->text, $value = $faker->text);
        static::assertEquals($name, $label->getName());
        static::assertEquals($value, $label->getValue());
    }
}
