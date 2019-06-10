<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Image;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Image\DockerAuthentication;
use Faker\Factory as FakerFactory;

class DockerAuthenticationTest extends TestCase
{
    public function testRegistry(): void
    {
        $faker = FakerFactory::create();
        $dockerAuthentication = new DockerAuthentication(
            $faker->userName,
            $faker->password
        );

        static::assertEquals('docker.io', $dockerAuthentication->getRegistry());

        $dockerAuthentication = new DockerAuthentication(
            $faker->userName,
            $faker->password,
            $registry = $faker->safeEmailDomain
        );

        static::assertEquals($registry, $dockerAuthentication->getRegistry());
    }

    public function testDockerCredential(): void
    {
        $faker = FakerFactory::create();
        $dockerAuthentication = new DockerAuthentication(
            $username = $faker->userName,
            $password = $faker->password,
            $registry = $faker->safeEmailDomain
        );

        $auth = base64_encode(
            json_encode(
                [
                    'serveraddress' => $registry,
                    'username' => $username,
                    'password' => $password,
                ]
            )
        );

        static::assertEquals($auth, $dockerAuthentication->getDockerCredential());
    }
}
