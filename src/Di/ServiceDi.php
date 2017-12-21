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

namespace Shadon\Di;

use Phalcon\Di\Service;
use Shadon\Dispatcher\ServiceDispatcher;
use Shadon\Http\PhalconServiceResponse as ServiceResponse;
use Shadon\Http\ServiceRequest;
use Shadon\Logger\ServiceLogger;
use Shadon\Mvc\Collection\Manager as CollectionManager;
use Shadon\Mvc\Model\Manager as ModelsManager;
use Shadon\Mvc\Model\Transaction\Manager as TransactionManager;
use Shadon\Router\ServiceRouter;

/**
 * @author hehui<hehui@eelly.net>
 */
class ServiceDi extends FactoryDefault
{
    public function __construct()
    {
        parent::__construct();
        $this->_services['collectionManager'] = new Service('collectionManager', CollectionManager::class, true);
        $this->_services['dispatcher'] = new Service('dispatcher', ServiceDispatcher::class, true);
        $this->_services['logger'] = new Service('logger', ServiceLogger::class, true);
        $this->_services['modelsManager'] = new Service('modelsManager', ModelsManager::class, true);
        $this->_services['request'] = new Service('request', ServiceRequest::class, true);
        $this->_services['response'] = new Service('response', ServiceResponse::class, true);
        $this->_services['router'] = new Service('router', ServiceRouter::class, true);
        $this->_services['transactionManager'] = new Service('transactionManager', TransactionManager::class, true);
    }
}
