<?php

namespace App;

use FuckingSmall\AbstractController;

class Controller extends AbstractController
{
    public function indexAction()
    {
        return $this->render('index');
    }
}
