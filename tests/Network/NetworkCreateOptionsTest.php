<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

use Faker\Factory as FakerFactory;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkCreateOptions;
use PHPUnit\Framework\TestCase;

class NetworkCreateOptionsTest extends TestCase
{
    public function testConstruct(): void
    {
        $networkCreateOptions = new NetworkCreateOptions();

        static::assertEquals('bridge', $networkCreateOptions->getDriver());
        static::assertEquals(false, $networkCreateOptions->isInternal());
        static::assertEquals(true, $networkCreateOptions->isAttachable());
        static::assertEquals(new LabelCollection(), $networkCreateOptions->getLabels());
    }

    public function testDriver(): void
    {
        $faker = FakerFactory::create();
        $networkCreateOptions = new NetworkCreateOptions();
        static::assertEquals(
            $networkCreateOptions,
            $networkCreateOptions->setDriver($driver = $faker->text)
        );
        static::assertEquals($driver, $networkCreateOptions->getDriver());
    }

    public function testInternal(): void
    {
        $networkCreateOptions = new NetworkCreateOptions();
        static::assertEquals(
            $networkCreateOptions,
            $networkCreateOptions->setInternal($internal = (bool) mt_rand(0, 1))
        );
        static::assertEquals($internal, $networkCreateOptions->isInternal());
    }

    public function testAttachable(): void
    {
        $networkCreateOptions = new NetworkCreateOptions();
        static::assertEquals(
            $networkCreateOptions,
            $networkCreateOptions->setAttachable($attachable = (bool) mt_rand(0, 1))
        );
        static::assertEquals($attachable, $networkCreateOptions->isAttachable());
    }

    public function testLabels(): void
    {
        $faker = FakerFactory::create();
        $labels = new LabelCollection();
        if (true === (bool) mt_rand(0, 1)) {
            for ($i = 0; $i <= 5; $i++) {
                $labels[] = new Label($faker->text, $faker->text);
            }
        }
        
        $networkCreateOptions = new NetworkCreateOptions();
        static::assertEquals($networkCreateOptions, $networkCreateOptions->setLabels($labels));
        static::assertEquals($labels, $networkCreateOptions->getLabels());
    }
    public function testAddLabel(): void
    {
        $faker = FakerFactory::create();
        $label = new Label($faker->text, $faker->text);

        $networkCreateOptions = new NetworkCreateOptions();
        static::assertEquals($networkCreateOptions, $networkCreateOptions->addLabel($label));
        static::assertEquals(
            new LabelCollection([$label]),
            $networkCreateOptions->getLabels()
        );
    }
}
