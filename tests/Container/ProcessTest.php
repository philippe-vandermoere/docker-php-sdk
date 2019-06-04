<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace Test\PhilippeVandermoere\DockerPhpSdk\Container;

use PHPUnit\Framework\TestCase;
use PhilippeVandermoere\DockerPhpSdk\Container\Process;
use Faker\Factory as FakerFactory;

class ProcessTest extends TestCase
{
    public function testConstruct(): void
    {
        $faker = FakerFactory::create();
        $process = new Process(
            $uid = mt_rand(0, 999),
            $pid = mt_rand(1, 65535),
            $ppid = mt_rand(1, 65535),
            $startTime = $faker->monthName . $faker->dayOfMonth,
            $tty = '?',
            $time = new \DateTimeImmutable($faker->time()),
            $cmd = $faker->text
        );

        static::assertEquals($uid, $process->getUid());
        static::assertEquals($pid, $process->getPid());
        static::assertEquals($ppid, $process->getPpid());
        static::assertEquals($startTime, $process->getStartTime());
        static::assertEquals($tty, $process->getTty());
        static::assertEquals($time, $process->getTime());
        static::assertEquals($cmd, $process->getCmd());
    }
}
