<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\ContainerCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Container;
use Faker\Factory as FakerFactory;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;
use steevanb\PhpTypedArray\Exception\KeyNotFoundException;

class ContainerCollectionTest extends TestCase
{
    public function testConstructWithoutContainer(): void
    {
        $containerCollection = new ContainerCollection();
        static::assertEquals(0, $containerCollection->count());
        static::assertEquals(Container::class, $containerCollection->getClassName());
        static::assertInstanceOf(ObjectArray::class, $containerCollection);
    }

    public function testConstructWithContainer(): void
    {
        $faker = FakerFactory::create();
        $values = [];
        for ($i = 0; $i <= 50; $i++) {
            $values[] = new Container($faker->uuid, $faker->text, $faker->text);
        }

        $containerCollection = new ContainerCollection($values);
        foreach ($containerCollection as $key => $container) {
            static::assertEquals($values[$key], $container);
        }

        static::assertEquals(count($values), $containerCollection->count());
    }

    public function testOffsetGetValidKey(): void
    {
        $faker = FakerFactory::create();
        $containerCollection = new ContainerCollection();
        $containerCollection->offsetSet(
            $key = mt_rand(0, PHP_INT_MAX),
            $container = new Container($faker->uuid, $faker->text, $faker->text)
        );

        static::assertEquals($container, $containerCollection->offsetGet($key));
    }

    public function testOffsetGetInvalidKey(): void
    {
        $containerCollection = new ContainerCollection();
        static::expectException(KeyNotFoundException::class);
        $containerCollection->offsetGet(mt_rand(0, PHP_INT_MAX));
    }

    public function testHasFalse(): void
    {
        $faker = FakerFactory::create();
        $containerCollection = new ContainerCollection();
        static::assertEquals(
            false,
            $containerCollection->has($faker->uuid)
        );
    }

    public function testHasTrue(): void
    {
        $faker = FakerFactory::create();
        $containerCollection = new ContainerCollection(
            [new Container($id = $faker->uuid, $faker->text, $faker->text)]
        );

        static::assertEquals(
            true,
            $containerCollection->has($id)
        );
    }
}
