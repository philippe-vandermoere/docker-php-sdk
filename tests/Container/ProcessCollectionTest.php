<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\ProcessCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Process;
use Faker\Factory as FakerFactory;
use steevanb\PhpTypedArray\Exception\KeyNotFoundException;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class ProcessCollectionTest extends TestCase
{
    public function testConstructWithoutProcess(): void
    {
        $processCollection = new ProcessCollection();
        static::assertEquals(0, $processCollection->count());
        static::assertEquals(Process::class, $processCollection->getClassName());
        static::assertInstanceOf(ObjectArray::class, $processCollection);
    }

    public function testConstructWithProcess(): void
    {
        $faker = FakerFactory::create();
        $values = [];
        for ($i = 0; $i <= 50; $i++) {
            $values[] = new Process(
                mt_rand(0, 999),
                mt_rand(1, 65535),
                mt_rand(1, 65535),
                $faker->monthName . $faker->dayOfMonth,
                '?',
                new \DateTimeImmutable($faker->time()),
                $faker->text
            );
        }

        $processCollection = new ProcessCollection($values);
        foreach ($processCollection as $key => $process) {
            static::assertEquals($values[$key], $process);
        }

        static::assertEquals(count($values), $processCollection->count());
    }

    public function testOffsetGetValidKey(): void
    {
        $faker = FakerFactory::create();
        $processCollection = new ProcessCollection();
        $processCollection->offsetSet(
            $key = mt_rand(0, PHP_INT_MAX),
            $process = new Process(
                mt_rand(0, 999),
                mt_rand(1, 65535),
                mt_rand(1, 65535),
                $faker->monthName . $faker->dayOfMonth,
                '?',
                new \DateTimeImmutable($faker->time()),
                $faker->text
            )
        );

        static::assertEquals($process, $processCollection->offsetGet($key));
    }

    public function testOffsetGetInvalidKey(): void
    {
        $processCollection = new ProcessCollection();
        static::expectException(KeyNotFoundException::class);
        $processCollection->offsetGet(mt_rand(0, PHP_INT_MAX));
    }
}
