<?php

namespace FrameworkModule;

use FuckingSmall\IoC\ContainerInterface;
use FuckingSmall\Dispatching\RouterInterface;

abstract class BaseModule
{
    abstract public function getName();

    abstract public function getPath();

    public function registerServices(ContainerInterface $container)
    {
        if (file_exists($this->getPath() . '/config/services.php')) {
            // ContainerInterface is used within services.php
            require_once $this->getPath() . '/config/services.php';
        }
    }

    public function registerRoutes(ContainerInterface $container)
    {
        if (file_exists($this->getPath() . '/config/routing.php')) {
            // RouterInterface is used within routing.php
            $router = $container->resolve(RouterInterface::class);
            require_once $this->getPath() . '/config/routing.php';
        }
    }
}