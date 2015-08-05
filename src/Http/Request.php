<?php

namespace FuckingSmall\Http;

/**
 * Class Request
 * @package FuckingSmall
 */
class Request implements RequestInterface
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param        $uri
     * @param string $method
     * @param array  $parameters
     */
    public function __construct($uri, $method = 'GET', array $parameters = [])
    {
        $this->uri        = $uri;
        $this->method     = $method;
        $this->parameters = $parameters;
    }

    /**
     * @return static
     */
    public static function createFromGlobals()
    {
        return new static(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            $_REQUEST
        );
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParameter($key, $default = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}