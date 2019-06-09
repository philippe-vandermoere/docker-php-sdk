<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

class Container
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $image;

    /** @var NetworkCollection */
    protected $networks;

    /** @var LabelCollection */
    protected $labels;

    public function __construct(string $id, string $name, string $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->networks = new NetworkCollection();
        $this->labels = new LabelCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setNetworks(NetworkCollection $networks): self
    {
        $this->networks = $networks;

        return $this;
    }

    public function getNetworks(): NetworkCollection
    {
        return $this->networks;
    }

    public function setLabels(LabelCollection $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function getLabels(): LabelCollection
    {
        return $this->labels;
    }
}
