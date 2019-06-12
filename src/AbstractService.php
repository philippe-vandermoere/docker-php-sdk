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
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;
use Psr\Http\Message\RequestInterface;

abstract class AbstractService
{
    use Json;

    protected const DOCKER_API_VERSION = 1.37;

    protected const CONTENT_TYPE_JSON = ['Content-Type' => 'application/json'];

    protected const CONTENT_TYPE_TAR = ['Content-Type' => 'application/x-tar'];

    /** @var HttpClient */
    protected $dockerClient;

    public function __construct(HttpClient $dockerClient)
    {
        $this->dockerClient = $dockerClient;
    }

    protected function sendRequest(string $method, string $route, array $headers = [], $body = null): string
    {
        if (null !== $body && 'application/json' === ($headers['Content-Type'] ?? '')) {
            $body = $this->jsonEncode($body);
        }

        $response = $this->dockerClient->sendRequest(
            $this->createRequest(
                $method,
                'http://' . static::DOCKER_API_VERSION . $route,
                $headers,
                $body
            )
        );

        if ($response->getStatusCode() >= 400) {
            throw new DockerException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response
            ->getBody()
            ->getContents()
        ;
    }

    protected function createRequest(string $method, string $uri, array $headers = [], $body = null): RequestInterface
    {
        return new Request(
            $method,
            $uri,
            $headers,
            $body
        );
    }
}
