<?php

namespace DemoModule;

use FrameworkModule\BaseController;

class Controller extends BaseController
{
    public function indexAction()
    {
        return $this->render('DemoModule:index');
    }
}
