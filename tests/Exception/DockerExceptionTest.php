<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Exception;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;
use Faker\Factory as FakerFactory;

class DockerExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $dockerException = new DockerException($message = $faker->text);

        static::assertInstanceOf(\Exception::class, $dockerException);
        static::assertEquals($message, $dockerException->getMessage());
    }
}
