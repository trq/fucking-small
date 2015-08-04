<?php

namespace FuckingSmall;

/**
 * An extremely simple IoC container implementation
 *
 * @package FuckingSmall
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Attach a service to the container
     *
     * @param $name
     * @param callable $callback
     *
     * @return $this
     */
    public function attach(string $name, callable $callback): ContainerInterface
    {
        $this->services[$name] = $callback;

        return $this;
    }

    /**
     * @param $alias
     * @param $concrete
     * @param callable $callback
     *
     * @return $this
     */
    public function alias(string $alias, string $concrete, callable $callback = null): ContainerInterface
    {
        $this->aliases[$alias] = $concrete;

        if (null !== $callback) {
            $this->services[$concrete] = $callback;
        }

        return $this;
    }

    /**
     * @param $index
     * @param mixed $value
     *
     * @return $this
     */
    public function setParameter(string $index, $value): ContainerInterface
    {
        $this->parameters[$index] = $value;

        return $this;
    }

    /**
     * Attempt to resolve a service, firstly from the container itself, then using reflection
     *
     * @param $name
     *
     * @return object
     */
    public function resolve(string $name): object
    {
        if (array_key_exists($name, $this->services)) {
            return call_user_func($this->services[$name]);
        } else if (array_key_exists($name, $this->aliases) && array_key_exists($this->aliases[$name], $this->services)) {
            return call_user_func($this->services[$this->aliases[$name]]);
        } else {
            try {
                if ($result = $this->autoResolve($name)) {
                    return $result;
                }

                return $this->autoResolveAlias($name);
            } catch (\ReflectionException $e) {
                return $this->autoResolveAlias($name);
            }
        }
    }

    /**
     * A simple helper to resolve dependencies given an array of dependents.
     *
     * @param array $params
     *
     * @return array
     */
    private function getDependencies(array $params): array
    {
        $args = [];
        foreach ($params as $param) {
            if (array_key_exists($param->name, $this->parameters)) {
                $args[] = $this->parameters[$param->name];
            } elseif ($param->getClass() && $object = $this->resolve($param->getClass()->name)) {
                $args[] = $object;
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }

        return $args;
    }

    /**
     * @param $name
     *
     * @return object
     */
    private function autoResolve(string $name): object
    {
        $object = null;
        $reflection = new \ReflectionClass($name);

        if ($reflection->isInstantiable()) {
            $construct = $reflection->getConstructor();
            if ($construct === null) {
                $object = new $name;
            } else {
                $dependencies = $this->getDependencies($construct->getParameters());
                $object       = $reflection->newInstanceArgs($dependencies);
            }
        }

        return $object;
    }

    /**
     * @param $name
     *
     * @return object
     */
    protected function autoResolveAlias(string $name): object
    {
        if (array_key_exists($name, $this->aliases)) {
            return $this->autoResolve($this->aliases[$name]);
        }
    }
}