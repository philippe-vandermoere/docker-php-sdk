<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Network;

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
            $driver = $faker->text
        );

        static::assertEquals($id, $network->getId());
        static::assertEquals($name, $network->getName());
        static::assertEquals($driver, $network->getDriver());
    }
}
