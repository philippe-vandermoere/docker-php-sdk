<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\AbstractService;

class NetworkService extends AbstractService
{
    public function list(): NetworkCollection
    {
        $networkCollection = new NetworkCollection();
        foreach ($this->jsonDecode($this->sendRequest('GET', '/networks')) as $data) {
            $networkCollection[] = new Network(
                $data->Id,
                $data->Name,
                $data->Driver
            );
        }

        return $networkCollection;
    }

    public function connectContainer(string $networkId, string $containerId): self
    {
        $this->sendRequest(
            'POST',
            '/networks/' . $networkId . '/connect',
            static::CONTENT_TYPE_JSON,
            ['Container' => $containerId]
        );

        return $this;
    }
}