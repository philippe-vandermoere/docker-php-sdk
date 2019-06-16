<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\ObjectArrayTrait;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use Faker\Factory as FakerFactory;

class ObjectArrayTraitTest extends TestCase
{
    public function testFilter(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection();

        for ($i = 0; $i <= 10; $i++) {
            $labelCollection[] = new Label($faker->text, $faker->text);
        }

        $filter = function (Label $label) {
            return true;
        };

        static::assertInstanceOf(LabelCollection::class, $labelCollection->filter($filter));
        static::assertEquals($labelCollection, $labelCollection->filter($filter));
    }

    public function testFilterEmpty(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection();

        for ($i = 0; $i <= 10; $i++) {
            $labelCollection[] = new Label($faker->text, $faker->text);
        }

        $filter = function (Label $label) {
            return false;
        };

        static::assertInstanceOf(LabelCollection::class, $labelCollection->filter($filter));
        static::assertEquals(new LabelCollection(), $labelCollection->filter($filter));
    }
}
