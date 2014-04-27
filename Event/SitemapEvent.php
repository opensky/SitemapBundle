<?php

namespace OpenSky\Bundle\SitemapBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SitemapEvent extends Event
{
    protected $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function get($argument)
    {
        return $this->arguments[$argument];
    }

    public function has($argument)
    {
        return isset($this->arguments[$argument]);
    }
}
