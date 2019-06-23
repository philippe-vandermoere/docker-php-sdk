<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use PhilippeVandermoere\DockerPhpSdk\AbstractService;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerBuildException;
use PhilippeVandermoere\DockerPhpSdk\Exception\DockerInvalidFormatException;

class ImageService extends AbstractService
{
    protected const REGEX_PATTERN_REPOSITORY = '[a-z0-9.\/\_-]+';

    protected const REGEX_PATTERN_TAG = '[a-z0-9.\_-]+';

    public function build(
        BuildContextInterface $buildContext,
        array $buildArguments = [],
        string $dockerfilePath = 'Dockerfile'
    ): string {
        $query = [
            'dockerfile' => $dockerfilePath,
            'q' => true,
        ];

        if (null !== $buildContext->getRemote()) {
            $query['remote'] = $buildContext->getRemote();
        }

        if (0 < count($buildArguments)) {
            $query['buildargs'] = $this->jsonEncode($buildArguments);
        }

        $response = $this->sendRequest(
            'POST',
            '/build?' . \http_build_query($query),
            static::CONTENT_TYPE_TAR,
            $buildContext->getStream()
        );

        $imageId = '';
        foreach (explode("\r\n", trim($response)) as $rawData) {
            $data = $this->jsonDecode($rawData);
            if (isset($data->errorDetail)) {
                throw new DockerBuildException(
                    trim($data->errorDetail->message) ?? 'Unknown error.',
                    $data->errorDetail->code ?? 255
                );
            }

            if (isset($data->stream) && 1 === preg_match('/^sha256:/', trim($data->stream))) {
                $imageId = str_replace('sha256:', '', trim($data->stream));
            }
        }

        if ('' === $imageId) {
            throw new DockerBuildException('Unable to get image Id.');
        }

        return $imageId;
    }

    public function tag(string $imageId, string $repository, string $tag): self
    {
        $this
            ->validateParameter(static::REGEX_PATTERN_REPOSITORY, $repository)
            ->validateParameter(static::REGEX_PATTERN_TAG, $tag)
        ;

        $this->sendRequest(
            'POST',
            '/images/' . $imageId . '/tag?' . \http_build_query(
                [
                    'repo' => $repository,
                    'tag' => $tag,
                ]
            )
        );

        return $this;
    }

    public function push(
        DockerAuthentication $dockerAuthentication,
        string $imageId,
        string $repository,
        string $tag
    ): self {
        $this->tag(
            $imageId,
            $dockerAuthentication->getRegistry() . '/' .$repository,
            $tag
        );

        $this->sendRequest(
            'POST',
            '/images/' . $dockerAuthentication->getRegistry() . '/' .$repository . ':' . $tag . '/push',
            ['X-Registry-Auth' => $dockerAuthentication->getDockerCredential()]
        );

        return $this;
    }

    protected function validateParameter(string $regex, string $value): self
    {
        if (1 !== preg_match('/' . $regex . '/', $value)) {
            throw new DockerInvalidFormatException(
                sprintf(
                    'Parameter `%s` must respect format `%s`.',
                    $value,
                    $regex
                )
            );
        }

        return $this;
    }
}
