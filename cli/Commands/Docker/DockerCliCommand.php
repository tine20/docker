<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerCliCommand extends DockerCommand{
    
    protected function configure() {
        $this
            ->setName('docker:cli')
            ->setDescription('start shell in service name eg db or web for tine20')
            ->setHelp('')
            ->addArgument('container', InputArgument::REQUIRED, 'The name of your container')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        


        $this->initCompose();
        
        $container = $input->getArgument('container');
        passthru($this->getComposeString() . ' exec ' . $container . ' sh', $err);


        

        return Command::SUCCESS;
    }

    
}

