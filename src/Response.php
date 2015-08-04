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
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent(string $content): ResponseInterface
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