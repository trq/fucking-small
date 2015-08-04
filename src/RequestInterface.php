<?php
/**
 * Created by PhpStorm.
 * User: trq
 * Date: 4/08/2015
 * Time: 10:22 PM
 */
namespace FuckingSmall;


/**
 * Class Request
 * @package FuckingSmall
 */
interface RequestInterface
{
    /**
     * @return static
     */
    public static function createFromGlobals(): RequestInterface;

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @param $uri
     *
     * @return $this
     */
    public function setUri(string $uri): RequestInterface;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod(string $method): RequestInterface;

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParameter(string $key, $default = null);
}