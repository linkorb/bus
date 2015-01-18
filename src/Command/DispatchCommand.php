<?php

namespace LinkORB\Component\Bus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LinkORB\Component\Bus\Bus;

class DispatchCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('bus:dispatch')
        ->setDescription('Dispatch a command')
        ->addArgument(
            'classname',
            InputArgument::REQUIRED,
            'Classname'
        )
        ->addOption(
            'parameters',
            'p',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Parameters (seperate multiple values with spaces)'
        )
        ->addOption(
            'globals',
            'g',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Globals (seperate multiple values with spaces)'
        )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $classname = $input->getArgument('classname');
        $parameters = $input->getOption('parameters');
        $globals = $input->getOption('globals');
        
        $arguments = array();
        foreach ($parameters as $parameter) {
            $part = explode('=', $parameter);
            $arguments[$part[0]] = $part[1];
        }
        
        $container = array();
        foreach ($globals as $global) {
            $part = explode('=', $global);
            $container[$part[0]] = $part[1];
        }

        $bus = new Bus($container);

        $output->writeln(
            '<info>Dispatching: '.$classname . '</info>'
        );
        $bus->dispatchFrom($classname, $arguments);
    }
}
