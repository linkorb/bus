<?php
namespace LinkORB\Component\Bus;

use LinkORB\Component\Bus\CommandInterface;

interface HandlerInterface
{
    public function handle(CommandInterface $command);
}
