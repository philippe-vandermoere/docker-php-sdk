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
        foreach ($this->jsonDecodeResponse($this->sendRequest('/containers/json')) as $data) {
            $containerCollection[] = $this->parseContainer($data);
        }

        return $containerCollection;
    }

    public function get(string $containerId): Container
    {
        return $this->parseContainer(
            $this->jsonDecodeResponse(
                $this->sendRequest('/containers/' . $containerId . '/json')
            )
        );
    }

    public function getProcess(string $containerId): ProcessCollection
    {
        $response = $this->jsonDecodeResponse(
            $this->sendRequest('/containers/' . $containerId . '/top')
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
            '/containers/' . $containerId . '/logs?' . http_build_query(
                [
                    'stdout' => $stdout,
                    'stderr' => $stderr,
                    'since' => ($since instanceof \DateTimeInterface) ? $since->getTimestamp() : 0,
                    'until' => ($until instanceof \DateTimeInterface) ? $until->getTimestamp() : 0,
                ]
            )
        )->getBody()->getContents());
    }

    public function start(string $containerId): self
    {
        $this->sendRequest('/containers/' . $containerId . '/start', 'POST');

        return $this;
    }

    public function stop(string $containerId): self
    {
        $this->sendRequest('/containers/' . $containerId . '/stop', 'POST');

        return $this;
    }

    public function restart(string $containerId): self
    {
        $this->sendRequest('/containers/' . $containerId . '/restart', 'POST');

        return $this;
    }

    public function executeCommand(
        string $containerId,
        array $command,
        string $workingDirectory = null
    ): string {
        $id = $this->jsonDecodeResponse(
            $this->sendRequest(
                '/containers/' . $containerId . '/exec',
                'POST',
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
            '/exec/' . $id . '/start',
            'POST',
            [
                'Detach' => false,
                'Tty' => true
            ]
        );

        $exitCode = $this->jsonDecodeResponse(
            $this->sendRequest(
                '/exec/' . $id . '/json'
            )
        )->ExitCode;

        if (false === \is_int($exitCode) || 0 !== $exitCode) {
            throw new \RuntimeException($response->getBody()->getContents(), $exitCode);
        }

        return $response->getBody()->getContents();
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
