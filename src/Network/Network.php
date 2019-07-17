<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;

class Network
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $driver;

    /** @var bool */
    protected $internal;

    /** @var bool */
    protected $attachable;

    /** @var LabelCollection */
    protected $labels;

    public function __construct(
        string $id,
        string $name,
        string $driver,
        bool $internal,
        bool $attachable,
        LabelCollection $labels
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->driver = $driver;
        $this->internal = $internal;
        $this->attachable = $attachable;
        $this->labels = $labels;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function isAttachable(): bool
    {
        return $this->attachable;
    }

    public function getLabels(): LabelCollection
    {
        return $this->labels;
    }
}
