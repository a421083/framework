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

namespace Eelly\Network;

use Eelly\Events\Listener\HttpServerListener;
use Phalcon\DiInterface;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Lock;
use swoole_http_request as SwooleHttpRequest;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Server.
 */
class HttpServer extends SwooleHttpServer
{
    /**
     * 事件列表.
     *
     * @var array
     */
    private const EVENTS = [
        'Start',
        'Shutdown',
        'WorkerStart',
        'WorkerStop',
        'Request',
        'Packet',
        'Close',
        'BufferFull',
        'BufferEmpty',
        'Task',
        'Finish',
        'PipeMessage',
        'WorkerError',
        'ManagerStart',
        'ManagerStop',
    ];

    /**
     * @var OutputInterface
     */
    private $output;

    private $listner;

    /**
     * @var DiInterface
     */
    private $di;

    private $lock;

    public function __construct(string $host, int $port)
    {
        parent::__construct($host, $port);
        $this->listner = new HttpServerListener();
        $this->lock = new Lock(SWOOLE_MUTEX);
        foreach (self::EVENTS as $event) {
            $this->on($event, [$this->listner, 'on'.$event]);
        }
    }

    public function registerRouter(): void
    {
        /* @var \Phalcon\Mvc\Router $router */
        $router = $this->di->getShared('router');
        // system
        $router->addPost('/_/:controller/:action', [
            'namespace'  => 'Eelly\\Controller',
            'controller' => 1,
            'action'     => 2,
        ]);
        // doc
        foreach ($this->di->getShared('config')->appBundles as $bundle) {
            $this->di->getShared($bundle)
                ->registerService()
                ->registerRouter();
        }
        // service api
        foreach ($this->di->getShared('config')->moduleList as $moduleName) {
            $namespace = ucfirst($moduleName);
            $router->addPost('/'.$moduleName.'/:controller/:action', [
                'namespace'  => $namespace,
                'module'     => $moduleName,
                'controller' => 1,
                'action'     => 2,
            ]);
        }
    }

    public function convertRequest(SwooleHttpRequest $swooleHttpRequest): void
    {
    }

    public function setProcessName(string $name): void
    {
        $processName = 'httpserver_'.$name;
        swoole_set_process_name($processName);
        $info = sprintf('%s "%s" %d', formatTime(), $processName, getmypid());
        $this->lock->lock();
        $this->output->writeln($info);
        $this->lock->unlock();
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * @return DiInterface
     */
    public function getDi(): DiInterface
    {
        return $this->di;
    }

    /**
     * @param DiInterface $di
     */
    public function setDi(DiInterface $di): void
    {
        $this->di = $di;
    }
}
