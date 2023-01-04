<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerDownCommand extends DockerCommand{
    
    protected function configure() {
        parent::configure();
        
        $this
            ->setName('docker:down')
            ->setDescription('destroy docker setup.  stop containers, remove containers and networks, volumes will persist')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        parent::execute($input, $output);
        passthru($this->getComposeString() . ' down', $err);

        return $err;
    }

    
}

