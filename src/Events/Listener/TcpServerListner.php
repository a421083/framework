<?php

declare(strict_types=1);

/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eelly\Events\Listener;

use Swoole\Server;

class TcpServerListner
{
    public function onStart(Server $server)
    {
    }

    public function onShutdown()
    {
    }

    public function onWorkerStart()
    {
    }

    public function onWorkerStop()
    {
    }

    public function onConnect()
    {
    }

    public function onReceive()
    {
    }

    public function onPacket()
    {
    }

    public function onClose()
    {
    }

    public function onBufferFull()
    {
    }

    public function onBufferEmpty()
    {
    }

    public function onTask()
    {
    }

    public function onFinish()
    {
    }

    public function onPipeMessage()
    {
    }

    public function onWorkerError()
    {
    }

    public function onManagerStart()
    {
    }

    public function onManagerStop()
    {
    }
}
