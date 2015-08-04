<?php

namespace FuckingSmall;

interface RouterInterface
{
    /**
     * @param string $name
     * @param string $rule
     * @param string $action
     * @param array  $options
     *
     * @return $this
     */
    public function attach($name, $rule, $action, array $options = []);

    /**
     * Attempt to resolve a route from a URL
     *
     * @param Request $request
     *
     * @return array|false
     */
    public function resolve(Request $request);
}