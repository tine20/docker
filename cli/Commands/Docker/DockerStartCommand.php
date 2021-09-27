<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerStartCommand extends DockerCommand{
    
    protected function configure() {
        $this
            ->setName('docker:start')
            ->setDescription('start docker setup.  pulls/builds images, creates containers, starts containers')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initDockerCommand();
        $io = new ConsoleStyle($input, $output);

        $this->getTineDir($io);
        $this->getDocserviceDir($io);
        $this->getBroadcasthubDir($io);
        $this->anotherConfig($io);

        passthru($this->getComposeString() . ' up -d', $err);
        
        return Command::SUCCESS;
    }

    
}

