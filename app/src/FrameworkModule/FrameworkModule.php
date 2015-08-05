<?php

namespace FrameworkModule;

class FrameworkModule extends BaseModule
{
    public function getName()
    {
        return 'FrameworkModule';
    }

    public function getPath()
    {
        return __DIR__;
    }
}