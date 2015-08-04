<?php

namespace FuckingSmall;

/**
 * A very simple HTTP abstraction
 *
 * @package FuckingSmall
 */
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
     * @param        $method
     * @param array  $parameters
     */
    public function __construct(string $uri, string $method = 'GET', array $parameters = [])
    {
        $this->uri        = $uri;
        $this->method     = $method;
        $this->parameters = $parameters;
    }

    /**
     * @return static
     */
    public static function createFromGlobals(): RequestInterface
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
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param $uri
     *
     * @return $this
     */
    public function setUri(string $uri): RequestInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod(string $method): RequestInterface
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParameter(string $key, $default = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        return $default;
    }
}