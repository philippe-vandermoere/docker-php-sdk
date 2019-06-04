<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Container;

class Process
{
    /** @var int */
    protected $uid;

    /** @var int */
    protected $pid;

    /** @var int */
    protected $ppid;

    /** @var string */
    protected $startTime;

    /** @var string */
    protected $tty;

    /** @var \DateTimeImmutable */
    protected $time;

    /** @var string */
    protected $cmd;

    public function __construct(
        int $uid,
        int $pid,
        int $ppid,
        string $startTime,
        string $tty,
        \DateTimeImmutable $time,
        string $cmd
    ) {
        $this->uid = $uid;
        $this->pid = $pid;
        $this->ppid = $ppid;
        $this->startTime = $startTime;
        $this->tty = $tty;
        $this->time = $time;
        $this->cmd = $cmd;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getPpid(): int
    {
        return $this->ppid;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getTty(): string
    {
        return $this->tty;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function getCmd(): string
    {
        return $this->cmd;
    }
}
