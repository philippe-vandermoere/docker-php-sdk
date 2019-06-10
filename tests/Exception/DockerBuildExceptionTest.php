<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Exception;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerBuildException;
use Faker\Factory as FakerFactory;

class DockerBuildExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $dockerBuildException = new DockerBuildException($message = $faker->text);

        static::assertInstanceOf(\RuntimeException::class, $dockerBuildException);
        static::assertEquals($message, $dockerBuildException->getMessage());
    }
}
