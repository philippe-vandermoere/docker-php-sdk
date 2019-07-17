<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Network\Network;
use Faker\Factory as FakerFactory;

class NetworkTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $network = new Network(
            $id = $faker->uuid,
            $name = $faker->text,
            $driver = $faker->text,
            $internal = (bool) mt_rand(0, 1),
            $attachable = (bool) mt_rand(0, 1),
            $labels = new LabelCollection([new Label($faker->text, $faker->text)])
        );

        static::assertEquals($id, $network->getId());
        static::assertEquals($name, $network->getName());
        static::assertEquals($driver, $network->getDriver());
        static::assertEquals($internal, $network->isInternal());
        static::assertEquals($attachable, $network->isAttachable());
        static::assertEquals($labels, $network->getLabels());
    }
}
