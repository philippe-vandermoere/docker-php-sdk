<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use Faker\Factory as FakerFactory;
use steevanb\PhpTypedArray\Exception\KeyNotFoundException;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class LabelCollectionTest extends TestCase
{
    public function testConstructWithoutLabel(): void
    {
        $labelCollection = new LabelCollection();
        static::assertEquals(0, $labelCollection->count());
        static::assertEquals(Label::class, $labelCollection->getClassName());
        static::assertInstanceOf(ObjectArray::class, $labelCollection);
    }

    public function testConstructWithLabel(): void
    {
        $faker = FakerFactory::create();
        $values = [];
        for ($i = 0; $i <= 50; $i++) {
            $values[] = new Label($faker->text, $faker->text);
        }

        $labelCollection = new LabelCollection($values);
        foreach ($labelCollection as $key => $label) {
            static::assertEquals($values[$key], $label);
        }

        static::assertEquals(count($values), $labelCollection->count());
    }

    public function testOffsetGetValidKey(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection();
        $labelCollection->offsetSet(
            $key = mt_rand(0, PHP_INT_MAX),
            $label = new Label($faker->text, $faker->text)
        );

        static::assertEquals($label, $labelCollection->offsetGet($key));
    }

    public function testOffsetGetInvalidKey(): void
    {
        $labelCollection = new LabelCollection();
        static::expectException(KeyNotFoundException::class);
        $labelCollection->offsetGet(mt_rand(0, PHP_INT_MAX));
    }

    public function testHasFalse(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection();
        static::assertEquals(
            false,
            $labelCollection->has($faker->word)
        );
    }

    public function testHasTrue(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection(
            [new Label($label = $faker->text, $faker->text)]
        );

        static::assertEquals(
            true,
            $labelCollection->has($label)
        );
    }

    public function testGetValue(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection(
            [new Label($label = $faker->text, $value = $faker->text)]
        );

        static::assertEquals(
            $value,
            $labelCollection->getValue($label)
        );
    }

    public function testGetValueNull(): void
    {
        $faker = FakerFactory::create();
        $labelCollection = new LabelCollection();
        static::assertEquals(
            null,
            $labelCollection->getValue($faker->word)
        );
    }
}
