<?php

namespace FuckingSmall\IoC;

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
    protected $templates = [];

    /**
     * Attach a service to the container
     *
     * @param          $name
     * @param callable $callback
     *
     * @return $this
     */
    public function attach($name, callable $callback)
    {
        $this->services[$name] = $callback;

        return $this;
    }

    /**
     * @param string   $alias
     * @param string   $concrete
     * @param callable $callback
     *
     * @return $this
     */
    public function alias($alias, $concrete, callable $callback = null)
    {
        $this->aliases[$alias] = $concrete;

        if (null !== $callback) {
            $this->services[$concrete] = $callback;
        }

        return $this;
    }

    /**
     * @param       $template
     * @param array $parameters
     */
    public function template($template, array $parameters)
    {
        $this->templates[$template] = $parameters;
    }

    /**
     * Attempt to resolve a service, firstly from the container itself, then using reflection
     *
     * @param $name
     *
     * @return object|null
     */
    public function resolve($name)
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
     * A simple helper to resolve dependencies.
     *
     * @param \ReflectionClass $class
     * @param $parameters
     *
     * @return array
     */
    private function getDependencies($class, $parameters)
    {
        $defaults = [];
        if ($parent = $class->getParentClass()) {
            if (array_key_exists($parent->name, $this->templates)) {
                $defaults = $this->templates[$parent->name];
            }
        }

        $args = [];
        foreach ($parameters as $parameter) {
            /**
             * Is the dependency available via a parent class template?
             */
            if (!empty($defaults) && array_key_exists($parameter->name, $defaults)) {
                $args[] = $defaults[$parameter->name];
            /**
             * Is the dependency another object?
             */
            } elseif ($parameter->getClass() && $object = $this->resolve($parameter->getClass()->name)) {
                $args[] = $object;
            /**
             * Do we have a default value?
             */
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            }
        }

        return $args;
    }

    /**
     * @param $name
     *
     * @return object
     */
    private function autoResolve($name)
    {
        $object = null;
        $class  = new \ReflectionClass($name);

        if ($class->isInstantiable()) {
            $construct = $class->getConstructor();
            if ($construct === null) {
                $object = new $name;
            } else {
                $dependencies = $this->getDependencies($class, $construct->getParameters());
                $object       = $class->newInstanceArgs($dependencies);
            }
        }

        return $object;
    }

    /**
     * @param $name
     *
     * @return object
     */
    protected function autoResolveAlias($name)
    {
        if (array_key_exists($name, $this->aliases)) {
            return $this->autoResolve($this->aliases[$name]);
        }
    }
}