<?php

namespace FuckingSmall\IoC;

class TaggedReference
{
    private $tag;

    /**
     * @param string $tag
     */
    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
}