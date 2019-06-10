<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk;

use PhilippeVandermoere\DockerPhpSdk\Exception\JsonException;
use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Json;
use Faker\Factory as FakerFactory;

class JsonTest extends TestCase
{
    public function testJsonEncodeValidJson(): void
    {
        $faker = FakerFactory::create();
        $jsonTrait = $this->getMockForTrait(Json::class);

        $data = [
            $faker->word,
            mt_rand(0, PHP_INT_MAX),
            new \stdClass(),
        ];

        $json = \json_encode($data);

        static::assertEquals(
            $json,
            $jsonTrait->jsonEncode($data)
        );
    }

    public function testJsonEncodeInvalidJson(): void
    {
        $jsonTrait = $this->getMockForTrait(Json::class);
        static::expectException(JsonException::class);
        $jsonTrait->jsonEncode([\chr(200)]);
    }

    public function testJsonDecodeValidJson(): void
    {
        $faker = FakerFactory::create();
        $jsonTrait = $this->getMockForTrait(Json::class);

        $json = \json_encode([
            'Id' => $id = $faker->uuid,
            'Name' => $name = $faker->word,
        ]);

        $data = new \stdClass();
        $data->Id = $id;
        $data->Name = $name;

        static::assertEquals(
            $data,
            $jsonTrait->jsonDecode($json)
        );
    }

    public function testJsonDecodeInvalidJson(): void
    {
        $jsonTrait = $this->getMockForTrait(Json::class);
        static::expectException(JsonException::class);
        $jsonTrait->jsonDecode('{aaaa');
    }
}
