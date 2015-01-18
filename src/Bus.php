<?php

namespace LinkORB\Component\Bus;

use LinkORB\Component\Bus\Exception\MissingArgumentException;
use LinkORB\Component\Bus\Exception\UnknownArgumentException;
use ReflectionClass;

class Bus
{
    private $container;
    
    public function __construct($container = array())
    {
        $this->container = $container;
    }
    public function dispatch(CommandInterface $command)
    {
        $this->handle($command);
    }

    private function instantiateFrom($classname, $data = array())
    {
        $arguments = array();
        
        $reflector = new ReflectionClass($classname);
        $method = $reflector->getConstructor();
        
        // Sanity checks
        foreach ($data as $key => $value) {
            $found = false;
            foreach ($method->getParameters() as $p) {
                if ($p->getName() == $key) {
                    $found = true;
                    //TODO: typechecking?
                }
            }
            if (!$found) {
                $message = "Unknown argument: " . $key . ". Expecting (";
                foreach ($method->getParameters() as $p) {
                    $message .= "\$" . $p->getName() . " ";
                }
                $message = trim($message) . ')';
                
                throw new UnknownArgumentException($message);
            }
        }
        
        // Build the constructor argument array
        $i = 0;
        foreach ($method->getParameters() as $p) {
            if (isset($data[$p->getName()])) {
                $arguments[$i] = $data[$p->getName()];
            } else {
                $arguments[$i] = null;
                if (!$p->isOptional()) {
                    throw new MissingArgumentException('Missing non-optional constructor argument \'$' . $p->getName() . '\' on new ' . $classname . '()');
                }
            }
        }
        //print_r($arguments);
        $instance = $reflector->newInstanceArgs($data);
        return $instance;
    }

    public function dispatchFrom($classname, $data = array())
    {
        $command = $this->instantiateFrom($classname, $data);
        $this->handle($command);
    }
    
    public function handle(CommandInterface $command)
    {
        $commandClassName = get_class($command);
        
        //TODO: support a command to handler mapping array
        $handlerClassName = str_replace('Command', 'Handler', $commandClassName);
        
        $handler = $this->instantiateFrom($handlerClassName, $this->container);
        $handler->handle($command);
    }
    
    public function getCommandParameters($classname)
    {
        $refl = new ReflectionClass($classname);
        $method = $refl->getConstructor();
        $comments = $method->getDocComment();
        echo $comments;
        
        foreach ($method->getParameters() as $p) {
            print_r($p);
        }
    }
}
