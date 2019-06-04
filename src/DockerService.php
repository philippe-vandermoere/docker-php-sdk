<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk;

use PhilippeVandermoere\DockerPhpSdk\Container\ContainerService;
use PhilippeVandermoere\DockerPhpSdk\Network\NetworkService;
use PhilippeVandermoere\DockerPhpSdk\Image\ImageService;

class DockerService
{
    /** @var ContainerService */
    protected $containerService;

    /** @var NetworkService */
    protected $networkService;

    /** @var ImageService */
    protected $imageService;

    public function __construct(
        ContainerService $containerService,
        NetworkService $networkService,
        ImageService $imageService
    ) {
        $this->containerService = $containerService;
        $this->networkService = $networkService;
        $this->imageService = $imageService;
    }

    public function getContainerService(): ContainerService
    {
        return $this->containerService;
    }

    public function getNetworkService(): NetworkService
    {
        return $this->networkService;
    }

    public function getImageService(): ImageService
    {
        return $this->imageService;
    }
}
