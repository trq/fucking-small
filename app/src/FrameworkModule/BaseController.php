<?php

namespace FrameworkModule;

use FuckingSmall\Http\Response;
use FuckingSmall\Http\ResponseInterface;

class BaseController
{
    /**
     * @var string
     */
    private $appPath;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param string            $appPath
     * @param ResponseInterface $response
     */
    public function __construct($appPath, ResponseInterface $response)
    {
        $this->appPath  = $appPath;
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
        list($module, $name) = explode(':', $name);

        /**
         * TODO: The module path needs to be resolved some other way. Relying on the appPath is shite.
         */
        $path = $this->appPath . '/src/' . $module . '/view/' . $name . '.php';

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