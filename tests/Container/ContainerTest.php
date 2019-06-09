<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\Container;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PhilippeVandermoere\DockerPhpSdk\Container\Network;
use PhilippeVandermoere\DockerPhpSdk\Container\NetworkCollection;
use Faker\Factory as FakerFactory;

class ContainerTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();

        $container = new Container(
            $id = $faker->uuid,
            $name = $faker->text,
            $image = $faker->text
        );

        static::assertEquals($id, $container->getId());
        static::assertEquals($name, $container->getName());
        static::assertEquals($image, $container->getImage());
        static::assertEquals(0, $container->getNetworks()->count());
        static::assertEquals(0, $container->getLabels()->count());
    }

    public function testLabels(): void
    {
        $faker = FakerFactory::create();
        $container = new Container($faker->uuid, $faker->text, $faker->text);
        $labels = new LabelCollection();
        for ($i = 0; $i <= 50; $i++) {
            $labels[] = new Label($faker->text, $faker->text);
        }

        static::assertEquals($container, $container->setLabels($labels));
        static::assertEquals($labels, $container->getLabels());
    }

    public function testNetworks(): void
    {
        $faker = FakerFactory::create();
        $container = new Container($faker->uuid, $faker->text, $faker->text);
        $networks = new NetworkCollection();
        for ($i = 0; $i <= 50; $i++) {
            $networks[] = new Network($faker->uuid, $faker->text, $faker->localIpv4);
        }

        static::assertEquals($container, $container->setNetworks($networks));
        static::assertEquals($networks, $container->getNetworks());
    }
}
