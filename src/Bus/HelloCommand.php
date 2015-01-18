<?php

namespace LinkORB\Component\Bus\Bus;

use LinkORB\Component\Bus\CommandInterface;

class HelloCommand implements CommandInterface
{
    private $name;
    private $greeting;
    
    /**
     * $name string lol
     */
    public function __construct($name, $greeting = "Hello")
    {
        $this->name = $name;
        $this->greeting = $greeting;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getGreeting()
    {
        return $this->greeting;
    }
}
