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

    protected $attributes = [];

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * Attach a service to the container
     *
     * @param          $name
     * @param callable $callback
     * @param array    $attributes
     *
     * @return $this
     */
    public function attach($name, callable $callback, array $attributes = [])
    {
        $this->services[$name]   = $callback;
        $this->attributes[$name] = $attributes;

        return $this;
    }

    /**
     * @param string   $alias
     * @param string   $concrete
     * @param callable $callback
     * @param array    $attributes
     *
     * @return $this
     */
    public function alias($alias, $concrete, callable $callback = null, array $attributes = [])
    {
        $this->aliases[$alias] = $concrete;

        if (null !== $callback) {
            $this->attach($concrete, $callback, $attributes);
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
        $object = null;

        if (array_key_exists($name, $this->services)) {
            $object = call_user_func($this->services[$name]);
        } else if (array_key_exists($name, $this->aliases) && array_key_exists($this->aliases[$name], $this->services)) {
            $object = call_user_func($this->services[$this->aliases[$name]]);
        } else {
            try {
                if ($result = $this->autoResolve($name)) {
                    $object = $result;
                } else{
                    $object = $this->autoResolveAlias($name);
                }
            } catch (\ReflectionException $e) {
                $object = $this->autoResolveAlias($name);
            }
        }

        $object = $this->handleCallAttributes($name, $object);

        return $object;
    }

    /**
     * @param $attribute
     * @param $key
     *
     * @return array
     */
    public function findByAttribute($attribute, $key)
    {
        $services = [];
        foreach ($this->attributes as $serviceIdentifier => $serviceAttributes) {
            if (array_key_exists($attribute, $serviceAttributes)) {
                if (in_array($key, $serviceAttributes[$attribute])) {
                    $services[] = $serviceIdentifier;
                }
            }
        }

        return $services;
    }

    /**
     * @param $serviceIdentifier
     * @param $attribute
     *
     * @return bool
     */
    public function hasAttribute($serviceIdentifier, $attribute)
    {
        if (array_key_exists($serviceIdentifier, $this->attributes)) {
            return array_key_exists($attribute, $this->attributes[$serviceIdentifier]);
        }

        return false;
    }

    /**
     * @param $serviceIdentifier
     * @param $attribute
     *
     * @return mixed
     */
    public function getAttribute($serviceIdentifier, $attribute)
    {
        if ($this->hasAttribute($serviceIdentifier, $attribute)) {
            return $this->attributes[$serviceIdentifier][$attribute];
        }
    }

    /**
     * @param $serviceIdentifier
     * @param $attribute
     * @param $value
     *
     * @return mixed
     */
    public function setAttribute($serviceIdentifier, $attribute, $value)
    {
        return $this->attributes[$serviceIdentifier][$attribute] = $value;
    }

    /**
     * @param $serviceIdentifier
     * @param $object
     */
    private function handleCallAttributes($serviceIdentifier, $object)
    {
        if ($this->hasAttribute($serviceIdentifier, 'calls')) {
            foreach ($this->getAttribute($serviceIdentifier, 'calls') as $method => $calls) {
                if (is_array($calls)) {
                    foreach ($calls as $arguments) {
                        if (is_array($arguments)) {

                            // See if we have any references
                            for($i =0; $i <= count($arguments); $i++) {
                                if ($arguments[$i] instanceof Reference) {
                                    $arguments[$i] = $this->resolve($arguments[$i]->getServiceIdentifier());
                                }
                            }

                            call_user_func_array([$object, $method], $arguments);
                        } else {
                            call_user_func([$object, $calls]);
                        }
                    }
                } else {
                    call_user_func([$object, $calls]);
                }
            }
        }

        return $object;
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
    private function autoResolveAlias($name)
    {
        if (array_key_exists($name, $this->aliases)) {
            return $this->autoResolve($this->aliases[$name]);
        }
    }
}