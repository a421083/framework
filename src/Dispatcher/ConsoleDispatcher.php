<?php
/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eelly\Dispatcher;

use Phalcon\Cli\Dispatcher;

/**
 * @author hehui<hehui@eelly.net>
 */
class ConsoleDispatcher extends Dispatcher
{
    public function afterServiceResolve(): void
    {
        $this->setTaskSuffix('Command');
    }
}