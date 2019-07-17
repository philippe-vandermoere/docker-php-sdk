<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkCollection;
use PhilippeVandermoere\DockerPhpSdk\Network\Network;
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
        $values = [];
        for ($i = 0; $i <= 10; $i++) {
            $network = $this->createNetwork();
            $values[$network->getId()] = $network;
        }

        $networkCollection = new NetworkCollection($values);
        foreach ($networkCollection as $key => $network) {
            static::assertEquals($values[$key], $network);
        }

        static::assertEquals(count($values), $networkCollection->count());
    }

    public function testOffsetGetValidKey(): void
    {
        $networkCollection = new NetworkCollection();
        $networkCollection->offsetSet(
            $key = mt_rand(0, PHP_INT_MAX),
            $network = $this->createNetwork()
        );

        static::assertEquals($network, $networkCollection->offsetGet($network->getId()));
    }

    public function testOffsetGetInvalidKey(): void
    {
        $networkCollection = new NetworkCollection();
        static::expectException(KeyNotFoundException::class);
        $networkCollection->offsetGet(mt_rand(0, PHP_INT_MAX));
    }

    protected function createNetwork(): Network
    {
        $faker = FakerFactory::create();
        return new Network(
            $faker->uuid,
            $faker->text,
            $faker->text,
            (bool) mt_rand(0, 1),
            (bool) mt_rand(0, 1),
            new LabelCollection()
        );
    }
}
