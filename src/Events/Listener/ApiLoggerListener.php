<?php

declare(strict_types=1);
/*
 * PHP version 7.1
 *
 * @copyright Copyright (c) 2012-2017 EELLY Inc. (https://www.eelly.com)
 * @link      https://api.eelly.com
 * @license   衣联网版权所有
 */

namespace Eelly\Events\Listener;

use Eelly\Http\Response;
use Eelly\SDK\Logger\Api\ApiLogger;
use MongoDB\BSON\ObjectID;
use Phalcon\Events\Event;
use Phalcon\Http\Response\Headers;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Dispatcher;

/**
 * api 日志.
 *
 * @author hehui<hehui@eelly.net>
 */
class ApiLoggerListener extends AbstractListener
{
    /**
     * 跟踪id.
     *
     * @var string
     */
    private $traceId;

    /**
     * 输入信息.
     *
     * @var array
     */
    private $requestData;

    /**
     * 输出信息.
     *
     * @var array
     */
    private $responseData;

    /**
     * 额外信息.
     *
     * @var array
     */
    private $extrasData;

    /**
     * 白名单.
     *
     * @var bool
     */
    private $isWhite = false;

    /**
     * @param Event       $event
     * @param Application $application
     * @param Dispatcher  $dispatcher
     */
    public function beforeHandleRequest(Event $event, Application $application, Dispatcher $dispatcher): void
    {
        $request = $this->request;
        $controllerClass = $dispatcher->getControllerClass();
        if (in_array($controllerClass, [
            'Oauth\Logic\AuthorizationserverLogic',
            'Oauth\Logic\ResourceserverLogic',
            'Logger\Logic\ApiloggerLogic',
        ])) {
            $this->isWhite = true;

            return;
        }
        $this->traceId = $request->getHeader('traceId');
        if (empty($this->traceId)) {
            $this->traceId = (new ObjectID())->__toString();
        } else {
            try {
                new ObjectID($this->traceId);
            } catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {
                $this->traceId = (new ObjectID())->__toString();
            }
        }
        // 添加跟踪id
        /**
         * @var \Eelly\SDK\EellyClient $eellyClient
         */
        $eellyClient = $this->getDI()->getEellyClient();
        $eellyClient->setTraceId($this->traceId);
        $this->requestData = [];
        $this->requestData['requestTime'] = $this->config->requestTime;
        $this->requestData['clientAddress'] = $request->getClientAddress(true);
        $this->requestData['serverAddress'] = $request->getServerAddress();
        $this->requestData['headers'] = $request->getHeaders();
        $this->requestData['URI'] = $request->getURI();
        $this->requestData['method'] = $request->getMethod();
        $this->requestData['post'] = $request->getPost();
        $this->requestData['moduleName'] = $dispatcher->getModuleName();
        $this->requestData['controllerClass'] = $controllerClass;
        $this->requestData['actionName'] = $dispatcher->getActionName();
        $this->requestData['paramss'] = $this->router->getParams();
    }

    /**
     * @param Event       $event
     * @param Application $application
     * @param Response    $response
     */
    public function beforeSendResponse(Event $event, Application $application, Response $response): void
    {
        if (true === $this->isWhite) {
            return;
        }
        $this->responseData['responseTime'] = microtime(true);
        $this->responseData['statusCode'] = $response->getStatusCode();
        $this->responseData['content'] = $response->getContent();
        $this->responseData['headers'] = $response->getHeaders()->toArray();
        $this->extrasData['usedTime'] = $this->responseData['responseTime'] - $this->requestData['requestTime'];
        $this->extrasData['usedMemory'] = memory_get_peak_usage(true);
        (new ApiLogger())->log($this->traceId, $this->requestData, $this->responseData, $this->extrasData);
    }
}
