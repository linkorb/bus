<?php

namespace LinkORB\Component\Bus\Bus;

use LinkORB\Component\Bus\Bus\Command\ExampleBusCommand;
use LinkORB\Component\Bus\CommandInterface;
use LinkORB\Component\Bus\HandlerInterface;

class HelloHandler implements HandlerInterface
{
    private $sender;
    
    public function __construct($sender)
    {
        $this->sender = $sender;
    }
    
    public function handle(CommandInterface $command)
    {
        echo $command->getGreeting() . ", " . $command->getName() . " from " . $this->sender . "!\n";
    }
}
