<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use PhilippeVandermoere\DockerPhpSdk\AbstractService;
use GuzzleHttp\Psr7\Request;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerException;

class ImageService extends AbstractService
{
    public function build(
        TarStream $tarStream,
        array $buildArgs = [],
        string $dockerfilePath = 'Dockerfile',
        string $dockerTag = null
    ): self {
        // @todo validate $dockerTag format
        $query = [
            'dockerfile' => $dockerfilePath,
            'buildargs' => $this->jsonEncode($buildArgs),
            't' => $dockerTag,
        ];

        $response = $this->dockerClient->sendRequest(
            new Request(
                'POST',
                'http://' . static::DOCKER_API_VERSION . '/build?' . \http_build_query($query),
                ['Content-Type' => 'application/x-tar'],
                $tarStream->getStream('.dockerignore')
            )
        );

        $tarStream->closeStream();

        if ($response->getStatusCode() >= 400) {
            throw new DockerException($response->getReasonPhrase(), $response->getStatusCode());
        }

        // @todo verify if error in build
        // @todo return image object with sha256n id, tag, ...

        //echo $response->getBody()->getContents();

        return $this;
    }
}
