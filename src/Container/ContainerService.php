<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

use PhilippeVandermoere\DockerPhpSdk\AbstractService;

class ContainerService extends AbstractService
{
    public function list(): ContainerCollection
    {
        $containerCollection = new ContainerCollection();
        foreach ($this->jsonDecode($this->sendRequest('GET', '/containers/json')) as $data) {
            $containerCollection[] = $this->parseContainer($data);
        }

        return $containerCollection;
    }

    public function get(string $containerId): Container
    {
        return $this->parseContainer(
            $this->jsonDecode($this->sendRequest('GET', '/containers/' . $containerId . '/json'))
        );
    }

    public function getProcess(string $containerId): ProcessCollection
    {
        $response = $this->jsonDecode(
            $this->sendRequest(
                'GET',
                '/containers/' . $containerId . '/top'
            )
        );

        $processCollection = new ProcessCollection();
        foreach ($response->Processes ?? [] as $data) {
            $processCollection[] = new Process(
                (int) $data[0],
                (int) $data[1],
                (int) $data[2],
                $data[4],
                $data[5],
                new \DateTimeImmutable($data[6]),
                $data[7]
            );
        }

        return $processCollection;
    }

    public function getLogs(
        string $containerId,
        bool $stdout = true,
        bool $stderr = true,
        \DateTimeInterface $since = null,
        \DateTimeInterface $until = null
    ): string {
        return trim($this->sendRequest(
            'GET',
            '/containers/' . $containerId . '/logs?' . http_build_query(
                [
                    'stdout' => $stdout,
                    'stderr' => $stderr,
                    'since' => ($since instanceof \DateTimeInterface) ? $since->getTimestamp() : 0,
                    'until' => ($until instanceof \DateTimeInterface) ? $until->getTimestamp() : 0,
                ]
            )
        ));
    }

    public function start(string $containerId): self
    {
        $this->sendRequest('POST', '/containers/' . $containerId . '/start');

        return $this;
    }

    public function stop(string $containerId): self
    {
        $this->sendRequest('POST', '/containers/' . $containerId . '/stop');

        return $this;
    }

    public function restart(string $containerId): self
    {
        $this->sendRequest('POST', '/containers/' . $containerId . '/restart');

        return $this;
    }

    public function executeCommand(
        string $containerId,
        array $command,
        string $workingDirectory = null
    ): string {
        $id = $this->jsonDecode(
            $this->sendRequest(
                'POST',
                '/containers/' . $containerId . '/exec',
                static::CONTENT_TYPE_JSON,
                [
                    'AttachStdin' => false,
                    'AttachStdout' => true,
                    'AttachStderr' => true,
                    'Tty' => true,
                    'Cmd' => $command,
                    'WorkingDir' => $workingDirectory
                ]
            )
        )->Id;

        $response = $this->sendRequest(
            'POST',
            '/exec/' . $id . '/start',
            static::CONTENT_TYPE_JSON,
            [
                'Detach' => false,
                'Tty' => true
            ]
        );

        $exitCode = $this->jsonDecode(
            $this->sendRequest(
                'GET',
                '/exec/' . $id . '/json'
            )
        )->ExitCode;

        if (false === \is_int($exitCode) || 0 !== $exitCode) {
            throw new \RuntimeException($response, $exitCode);
        }

        return $response;
    }

    protected function parseContainer(\stdClass $data): Container
    {
        $container = new Container(
            $data->Id,
            \ltrim($data->Name ?? $data->Names[0], '/'),
            $data->Image
        );

        $container->setNetworks($this->parseNetworks($data));
        $container->setLabels($this->parseLabels($data));

        return $container;
    }

    protected function parseNetworks(\stdClass $data): NetworkCollection
    {
        $networkCollection = new NetworkCollection();
        foreach ($data->NetworkSettings->Networks ?? [] as $name => $dataNetwork) {
            $networkCollection[] = new Network(
                $dataNetwork->NetworkID,
                $name,
                $dataNetwork->IPAddress
            );
        }

        return $networkCollection;
    }

    protected function parseLabels(\stdClass $data): LabelCollection
    {
        $labelCollection = new LabelCollection();
        foreach ($data->Labels ?? [] as $name => $value) {
            $labelCollection[] = new Label($name, $value);
        }

        return $labelCollection;
    }
}
