<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Exception;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Exception\JsonException;

class JsonExceptionTest extends TestCase
{
    /** @dataProvider getData */
    public function testConstruct(int $jsonError, string $expectedMessage): void
    {
        $jsonException = new JsonException($jsonError);

        static::assertInstanceOf(\Exception::class, $jsonException);
        static::assertEquals($expectedMessage, $jsonException->getMessage());
    }

    public function getData(): array
    {
        return [
            [JSON_ERROR_DEPTH, 'Maximum stack depth exceeded.'],
            [JSON_ERROR_STATE_MISMATCH, 'Underflow or the modes mismatch.'],
            [JSON_ERROR_CTRL_CHAR, 'Unexpected control character found.'],
            [JSON_ERROR_SYNTAX, 'Syntax error, malformed JSON.'],
            [JSON_ERROR_UTF8, 'Malformed UTF-8 characters, possibly incorrectly encoded.'],
            [PHP_INT_MAX, 'Unknown error.'],
        ];
    }
}
