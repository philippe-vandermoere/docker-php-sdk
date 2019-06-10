<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

namespace PhilippeVandermoere\DockerPhpSdk;

use PhilippeVandermoere\DockerPhpSdk\Exception\JsonException;

trait Json
{
    public function jsonEncode($data): string
    {
        $json = \json_encode($data);

        if (false === $json) {
            throw new JsonException(\json_last_error());
        }

        return $json;
    }

    public function jsonDecode(string $json)
    {
        $data = \json_decode($json);

        if (null === $data) {
            throw new JsonException(\json_last_error());
        }

        return $data;
    }
}
