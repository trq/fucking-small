<?php

namespace FuckingSmall;

class Dispatcher implements DispatcherInterface
{
    /**
     * @param ContainerInterface $container
     * @param array              $payload
     *
     * @return string|null
     */
    public function dispatch(ContainerInterface $container, array $payload)
    {
        $controllerClass = $payload['_controller'];
        $method          = $payload['_method'];

        if (substr($method, -6) !== 'Action') {
            $method .= 'Action';
        }

        $reflection = new \ReflectionMethod($controllerClass, $method);

        $args = [];
        foreach ($reflection->getParameters() as $param) {
            if (array_key_exists($param->name, $payload)) {
                $args[] = $payload[$param->name];
            } elseif ($param->getClass() && $object = $container->resolve($param->getClass()->name)) {
                $args[] = $object;
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }

        if ($controller = $container->resolve($controllerClass)) {
            return $reflection->invokeArgs(
                $controller,
                $args
            );
        }
    }
}