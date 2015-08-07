<?php

namespace FuckingSmallTest\Fixture;

class ServiceWithCall
{
    private $something;
    private $somethingElse;

    public function setSomething($something)
    {
        $this->something = $something;
    }

    public function getSomething()
    {
        return $this->something;
    }

    public function setSomethingElse($a, $b)
    {
        $this->somethingElse = $a . $b;
    }

    public function getSomethingElse()
    {
        return $this->somethingElse;
    }

    public function set()
    {
        $this->something = 'has been set';
    }

    public function get()
    {
        return $this->something;
    }
}