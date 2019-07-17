<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;

class NetworkCreateOptions
{
    /** @var string */
    protected $driver;

    /** @var bool */
    protected $internal;

    /** @var bool */
    protected $attachable;

    /** @var LabelCollection */
    protected $labels;

    public function __construct()
    {
        $this->driver = 'bridge';
        $this->internal = false;
        $this->attachable = true;
        $this->labels = new LabelCollection();
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;

        return $this;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function setAttachable(bool $attachable): self
    {
        $this->attachable = $attachable;

        return $this;
    }

    public function isAttachable(): bool
    {
        return $this->attachable;
    }

    public function setLabels(LabelCollection $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function addLabel(Label $label): self
    {
        $this->labels[] = $label;

        return $this;
    }

    public function getLabels(): LabelCollection
    {
        return $this->labels;
    }
}
