<?php

namespace FuckingSmall;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     *
     */
    public function send()
    {
        echo $this->content;
    }
}