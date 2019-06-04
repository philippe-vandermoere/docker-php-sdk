<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\Network;
use Faker\Factory as FakerFactory;

class NetworkTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $network = new Network(
            $id = $faker->uuid,
            $name = $faker->text,
            $ip = $faker->localIpv4
        );

        static::assertEquals($id, $network->getId());
        static::assertEquals($name, $network->getName());
        static::assertEquals($ip, $network->getIp());
    }
}
