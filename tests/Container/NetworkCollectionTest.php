<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\NetworkCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Network;
use Faker\Factory as FakerFactory;
use steevanb\PhpTypedArray\Exception\KeyNotFoundException;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class NetworkCollectionTest extends TestCase
{
    public function testConstructWithoutNetwork(): void
    {
        $networkCollection = new NetworkCollection();
        static::assertEquals(0, $networkCollection->count());
        static::assertEquals(Network::class, $networkCollection->getClassName());
        static::assertInstanceOf(ObjectArray::class, $networkCollection);
    }

    public function testConstructWithNetwork(): void
    {
        $faker = FakerFactory::create();
        $values = [];
        for ($i = 0; $i <= 50; $i++) {
            $values[] = new Network($faker->uuid, $faker->text, $faker->localIpv4);
        }

        $networkCollection = new NetworkCollection($values);
        foreach ($networkCollection as $key => $network) {
            static::assertEquals($values[$key], $network);
        }

        static::assertEquals(count($values), $networkCollection->count());
    }

    public function testOffsetGetValidKey(): void
    {
        $faker = FakerFactory::create();
        $networkCollection = new NetworkCollection();
        $networkCollection->offsetSet(
            $key = mt_rand(0, PHP_INT_MAX),
            $network = new Network($faker->uuid, $faker->text, $faker->localIpv4)
        );

        static::assertEquals($network, $networkCollection->offsetGet($key));
    }

    public function testOffsetGetInvalidKey(): void
    {
        $networkCollection = new NetworkCollection();
        static::expectException(KeyNotFoundException::class);
        $networkCollection->offsetGet(mt_rand(0, PHP_INT_MAX));
    }
}
