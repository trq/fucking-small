<?php

namespace FuckingSmall;

interface ResponseInterface
{
    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param string $content
     */
    public function setContent($content): ResponseInterface;

    /**
     *
     */
    public function send();
}