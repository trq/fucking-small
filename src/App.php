<?php

namespace FuckingSmall;

class App
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string             $appPath
     * @param ContainerInterface $container
     */
    public function __construct($appPath, ContainerInterface $container = null) {
        if (null === $container) {
            $container = new Container();
        }

        $this->container = $container;
        $this->container->setParameter('_appPath', $appPath);
        $this->bootstrap($appPath);
    }

    /**
     *
     */
    public function run()
    {
        $router     = $this->container->resolve(RouterInterface::class);
        $request    = $this->container->resolve(RequestInterface::class);
        $dispatcher = $this->container->resolve(DispatcherInterface::class);

        $response = null;
        if ($payload = $router->resolve($request)) {
            $response = $dispatcher->dispatch($this->container, $payload);

            if (null === $response || !$response instanceof Response) {
                throw new \RuntimeException('Controllers must return a ResponseInterface implementation');
            }

            $response->send();
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Bootstrap the framework's Request, Router and Dispatch dependencies
     *
     * @param string appPath
     */
    protected function bootstrap($appPath)
    {
        $this->container
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


        /**
         * Bootstrap services and register routes with the router
         */
        $container = $this->container;
        if (file_exists($appPath . '/services.php')) {
            require_once $appPath . '/services.php';
        }
        unset($container);

        $router = $this->container->resolve(RouterInterface::class);
        if (file_exists($appPath . '/routing.php')) {
            require_once $appPath . '/routing.php';
        }
        unset($router);
    }
}
