<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Network;

use PhilippeVandermoere\DockerPhpSdk\AbstractService;
use PhilippeVandermoere\DockerPhpSdk\Container\Label;
use PhilippeVandermoere\DockerPhpSdk\Container\LabelCollection;

class NetworkService extends AbstractService
{
    public function list(): NetworkCollection
    {
        $networkCollection = new NetworkCollection();
        foreach ($this->jsonDecode($this->sendRequest('GET', '/networks')) as $data) {
            $networkCollection[] = $this->parseNetwork($data);
        }

        return $networkCollection;
    }

    public function get(string $networkId): Network
    {
        return $this->parseNetwork(
            $this->jsonDecode(
                $this->sendRequest(
                    'GET',
                    '/networks/' . $networkId
                )
            )
        );
    }

    public function create(string $name, NetworkCreateOptions $networkCreateOptions = null): Network
    {
        if (false === $networkCreateOptions instanceof NetworkCreateOptions) {
            $networkCreateOptions = new NetworkCreateOptions();
        }

        $body = [
            'Name' => $name,
            'Drivers' => $networkCreateOptions->getDriver(),
            'Internal' => $networkCreateOptions->isInternal(),
            'Attachable' => $networkCreateOptions->isAttachable(),
        ];

        foreach ($networkCreateOptions->getLabels() as $label) {
            $body['Labels'][$label->getName()] = $label->getValue();
        }

        return $this->get(
            $this->jsonDecode(
                $this->sendRequest(
                    'POST',
                    '/networks/create',
                    static::CONTENT_TYPE_JSON,
                    $body
                )
            )->Id
        );
    }

    public function remove(string $networkId): self
    {
        $this->sendRequest(
            'DELETE',
            '/networks/' . $networkId
        );

        return $this;
    }

    public function connectContainer(string $networkId, string $containerId, array $aliases = []): self
    {
        $body = ['Container' => $containerId];

        if (0 < count($aliases)) {
            $body['EndpointConfig']['Aliases'] = $aliases;
        }

        $this->sendRequest(
            'POST',
            '/networks/' . $networkId . '/connect',
            static::CONTENT_TYPE_JSON,
            $body
        );

        return $this;
    }

    public function disconnectContainer(string $networkId, string $containerId): self
    {
        $this->sendRequest(
            'POST',
            '/networks/' . $networkId . '/disconnect',
            static::CONTENT_TYPE_JSON,
            ['Container' => $containerId]
        );

        return $this;
    }

    protected function parseNetwork(\stdClass $data): Network
    {
        $labels = new LabelCollection();
        foreach ($data->Labels ?? [] as $name => $value) {
            $labels[] = new Label($name, $value);
        }

        return new Network(
            $data->Id,
            $data->Name,
            $data->Driver,
            $data->Internal,
            $data->Attachable,
            $labels
        );
    }
}
