<?php

namespace FuckingSmall\Http;

interface RequestInterface
{
    /**
     * @return static
     */
    public static function createFromGlobals();

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param string $uri
     */
    public function setUri($uri);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $method
     */
    public function setMethod($method);

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParameter($key, $default = null);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     */
    public function setParameters($parameters);
}