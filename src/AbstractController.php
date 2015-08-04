<?php

namespace FuckingSmall;

class AbstractController
{
    /**
     * @var
     */
    private $appPath;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param string            $_appPath
     * @param ResponseInterface $response
     */
    public function __construct($_appPath, ResponseInterface $response)
    {
        $this->appPath  = $_appPath;
        $this->response = $response;
    }

    /**
     * @param       $name
     * @param array $vars
     *
     * @return Response
     */
    protected function render($name, array $vars = [])
    {
        $path = $this->appPath . '/view/' . $name . '.php';

        if (file_exists($path)) {
            extract($vars);
            ob_start();
            include $path;
            $this->response->setContent(ob_get_clean());

            return $this->response;
        } else {
            throw new \RuntimeException("view '$path' not found");
        }
    }
}