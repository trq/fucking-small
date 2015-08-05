<?php

namespace FuckingSmall\Dispatching;

use FuckingSmall\IoC\ContainerInterface;

interface DispatcherInterface
{
    /**
     * @param ContainerInterface $container
     * @param array              $payload
     *
     * @return string
     */
    public function dispatch(ContainerInterface $container, array $payload);
}