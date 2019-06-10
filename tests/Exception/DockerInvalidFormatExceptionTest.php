<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Exception;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerInvalidFormatException;
use Faker\Factory as FakerFactory;

class DockerInvalidFormatExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $dockerInvalidFormatException = new DockerInvalidFormatException($message = $faker->text);

        static::assertInstanceOf(\InvalidArgumentException::class, $dockerInvalidFormatException);
        static::assertEquals($message, $dockerInvalidFormatException->getMessage());
    }
}
