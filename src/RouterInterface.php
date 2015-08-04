<?php

namespace FuckingSmall;

interface RouterInterface
{
    /**
     * @param $name
     * @param $rule
     * @param $action
     * @param array  $options
     *
     * @return $this
     */
    public function attach(string $name, string $rule, string $action, array $options = []): RouterInterface;

    /**
     * Attempt to resolve a route from a URL
     *
     * @param Request $request
     *
     * @return array|false
     */
    public function resolve(Request $request);
}