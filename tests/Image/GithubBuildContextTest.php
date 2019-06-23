<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Image;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Image\GithubBuildContext;
use Faker\Factory as FakerFactory;

class GithubBuildContextTest extends TestCase
{
    public function testGetRemote(): void
    {
        $faker = FakerFactory::create();
        $githubBuildContext = new GithubBuildContext(
            $organisation = $faker->word,
            $repository = $faker->word
        );
        static::assertEquals(
            'https://github.com/' . $organisation . '/' . $repository . '.git#master',
            $githubBuildContext->getRemote()
        );
    }

    public function testGetRemoteWithReference(): void
    {
        $faker = FakerFactory::create();
        $githubBuildContext = new GithubBuildContext(
            $organisation = $faker->word,
            $repository = $faker->word,
            $reference = $faker->word
        );
        static::assertEquals(
            'https://github.com/' . $organisation . '/' . $repository . '.git#' . $reference,
            $githubBuildContext->getRemote()
        );
    }

    public function testGetRemoteWithDirectory(): void
    {
        $faker = FakerFactory::create();
        $githubBuildContext = new GithubBuildContext(
            $organisation = $faker->word,
            $repository = $faker->word,
            $reference = $faker->word,
            $directory = $faker->word . '/' . $faker->word
        );
        static::assertEquals(
            'https://github.com/' . $organisation . '/' . $repository . '.git#' . $reference . ':' . $directory,
            $githubBuildContext->getRemote()
        );
    }

    public function testGetRemoteWithToken(): void
    {
        $faker = FakerFactory::create();
        $githubBuildContext = new GithubBuildContext(
            $organisation = $faker->word,
            $repository = $faker->word,
            $reference = $faker->word,
            $directory = $faker->word . '/' . $faker->word,
            $token = $faker->password
        );
        static::assertEquals(
            sprintf(
                'https://%s:@github.com/%s/%s.git#%s:%s',
                $token,
                $organisation,
                $repository,
                $reference,
                $directory
            ),
            $githubBuildContext->getRemote()
        );
    }

    public function testGetStream(): void
    {
        $faker = FakerFactory::create();
        $githubBuildContext = new GithubBuildContext(
            $faker->word,
            $faker->word
        );
        static::assertEquals(null, $githubBuildContext->getStream());
    }
}
