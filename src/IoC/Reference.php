<?php

namespace FuckingSmall\IoC;

class Reference
{
    private $serviceIdentifier;

    /**
     * @param string $serviceIdentifier
     */
    public function __construct($serviceIdentifier)
    {
        $this->serviceIdentifier = $serviceIdentifier;
    }

    /**
     * @return string
     */
    public function getServiceIdentifier()
    {
        return $this->serviceIdentifier;
    }
}