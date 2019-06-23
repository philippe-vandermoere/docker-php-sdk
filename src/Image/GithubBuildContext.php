<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use GuzzleHttp\Psr7\Stream;

class GithubBuildContext implements BuildContextInterface
{
    protected const GITHUB_URL = 'github.com';

    /** @var string */
    protected $remote;

    public function __construct(
        string $organisation,
        string $repository,
        string $reference = 'master',
        string $directory = null,
        string $token = null
    ) {
        $this->remote = sprintf(
            'https://%sgithub.com/%s/%s.git#%s%s',
            (null === $token) ? '' : $token . ':@',
            $organisation,
            $repository,
            $reference,
            (null === $directory) ? '' : ':' . $directory
        );
    }

    public function getRemote(): ?string
    {
        return $this->remote;
    }

    public function getStream(): ?Stream
    {
        return null;
    }
}
