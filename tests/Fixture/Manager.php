<?php

namespace FuckingSmallTest\Fixture;

class Manager
{
    private $services;

    public function addService(\StdClass $service)
    {
        $this->services[] = $service;
    }

    public function getServices()
    {
        return $this->services;
    }
}