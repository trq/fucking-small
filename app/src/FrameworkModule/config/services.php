<?php

use FuckingSmall\Http\RequestInterface;
use FuckingSmall\Http\Request;
use FuckingSmall\Http\ResponseInterface;
use FuckingSmall\Http\Response;
use FuckingSmall\Dispatching\RouterInterface;
use FuckingSmall\Dispatching\Router;
use FuckingSmall\Dispatching\DispatcherInterface;
use FuckingSmall\Dispatching\Dispatcher;

$container
    /**
     * Alias the RequestInterface to the Request concrete implementation
     */
    ->alias(RequestInterface::class, Request::class, function () {
        /**
         * We want the Request object to be a singleton, so use a static variable to store it.
         */
        static $request;

        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        return $request;
    })
    /**
     * Alias the ResponseInterface to the Response concrete implementation
     */
    ->alias(ResponseInterface::class, Response::class, function () {
        /**
         * We want the Response object to be a singleton, so use a static variable to store it.
         */
        static $response;

        if (null === $response) {
            $response = new Response();
        }

        return $response;
    })
    /**
     * Alias the RouterInterface to the Router concrete implementation
     */
    ->alias(RouterInterface::class, Router::class, function () {
        /**
         * We want the Router object to be a singleton, so use a static variable to store it.
         */
        static $router;

        if (null === $router) {
            $router = new Router();
        }

        return $router;
    })
    ->alias(DispatcherInterface::class, Dispatcher::class);