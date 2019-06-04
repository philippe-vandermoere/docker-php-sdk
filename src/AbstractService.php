<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk;

use Http\Client\HttpClient;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;
use PhilippeVandermoere\DockerPhpSdk\Exception\JsonException;

abstract class AbstractService
{
    protected const DOCKER_API_VERSION = 1.37;

    /** @var HttpClient */
    protected $dockerClient;

    public function __construct(HttpClient $dockerClient)
    {
        $this->dockerClient = $dockerClient;
    }

    protected function sendRequest(string $route, string $method = 'GET', array $data = []): ResponseInterface
    {
        $response = $this->dockerClient->sendRequest(
            new Request(
                $method,
                'http://' . static::DOCKER_API_VERSION . $route,
                \count($data) > 0 ? ['Content-Type' => 'application/json'] : [],
                \count($data) > 0 ? $this->jsonEncode($data) : null
            )
        );

        if ($response->getStatusCode() >= 400) {
            throw new DockerException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }

    protected function jsonEncode(array $data): string
    {
        $json = \json_encode($data);

        if (false === $json) {
            throw new JsonException(\json_last_error());
        }

        return $json;
    }

    protected function jsonDecode(string $json)
    {
        $data = \json_decode($json);

        if (null === $data) {
            throw new JsonException(\json_last_error());
        }

        return $data;
    }

    protected function jsonDecodeResponse(ResponseInterface $response)
    {
        return $this->jsonDecode($response->getBody()->getContents());
    }
}
