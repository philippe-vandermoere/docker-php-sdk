<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Image;

use PhilippeVandermoere\DockerPhpSdk\Json;

class DockerAuthentication
{
    use Json;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var string */
    protected $registry;

    public function __construct(string $username, string $password, string $registry = 'docker.io')
    {
        $this->username = $username;
        $this->password = $password;
        $this->registry = $registry;
    }

    public function getRegistry(): string
    {
        return $this->registry;
    }

    public function getDockerCredential(): string
    {
        return \base64_encode(
            $this->jsonEncode(
                [
                    'serveraddress' => $this->registry,
                    'username' => $this->username,
                    'password' => $this->password,
                ]
            )
        );
    }
}
