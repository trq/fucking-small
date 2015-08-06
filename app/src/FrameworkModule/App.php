<?php

namespace FrameworkModule;

use FuckingSmall\appPath;
use FuckingSmall\Dispatching\DispatcherInterface;
use FuckingSmall\Dispatching\RouterInterface;
use FuckingSmall\Http\RequestInterface;
use FuckingSmall\Http\Response;
use FuckingSmall\IoC\Container;
use FuckingSmall\IoC\ContainerInterface;

class App
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @param string             $appPath
     * @param ContainerInterface $container
     */
    public function __construct($appPath, ContainerInterface $container = null) {
        if (null === $container) {
            $container = new Container();
        }

        $this->container = $container;
        $this->container->template(BaseController::class, ['appPath' => $appPath]);
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
     * @param array $modules
     */
    public function bootstrap(array $modules)
    {
        foreach ($modules as $module) {
            $this->modules[$module->getName()] = $module;
            $module->registerServices($this->container);
            $module->registerRoutes($this->container);
        }
    }
}
