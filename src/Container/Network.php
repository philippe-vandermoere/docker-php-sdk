<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

class Network
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $ip;

    /** @var array */
    protected $aliases;

    public function __construct(string $id, string $name, string $ip, array $aliases = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->ip = $ip;
        $this->aliases = $aliases;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }
}
