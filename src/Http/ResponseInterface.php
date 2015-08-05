<?php

namespace FuckingSmall\Http;

interface ResponseInterface
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     *
     */
    public function send();
}