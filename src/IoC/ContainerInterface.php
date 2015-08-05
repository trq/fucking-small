<?php

namespace FuckingSmall\IoC;

interface ContainerInterface
{
    /**
     * Attach a service to the container
     *
     * @param          $name
     * @param callable $callback
     *
     * @return $this
     */
    public function attach($name, callable $callback);

    /**
     * Attempt to resolve a service, firstly from the container itself, then using reflection
     *
     * @param $name
     *
     * @return object|null
     */
    public function resolve($name);
}