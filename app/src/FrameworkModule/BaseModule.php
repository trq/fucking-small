<?php

namespace FrameworkModule;

use FuckingSmall\IoC\ContainerInterface;
use FuckingSmall\Dispatching\RouterInterface;

abstract class BaseModule
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $path;

    /**
     * @param ContainerInterface $container
     */
    public function registerServices(ContainerInterface $container)
    {
        if (file_exists($this->getPath() . '/config/services.php')) {
            // ContainerInterface is used within services.php
            require_once $this->getPath() . '/config/services.php';
        }
    }

    /**
     * @param ContainerInterface $container
     */
    public function registerRoutes(ContainerInterface $container)
    {
        if (file_exists($this->getPath() . '/config/routing.php')) {
            // RouterInterface is used within routing.php
            $router = $container->resolve(RouterInterface::class);
            require_once $this->getPath() . '/config/routing.php';
        }
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = dirname($reflected->getFileName());
        }

        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name : substr($name, $pos + 1);
    }
}