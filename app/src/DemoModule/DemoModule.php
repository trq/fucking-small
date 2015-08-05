<?php

namespace DemoModule;

use FrameworkModule\BaseModule;

class DemoModule extends BaseModule
{
    public function getName()
    {
        return 'DemoModule';
    }

    public function getPath()
    {
        return __DIR__;
    }
}