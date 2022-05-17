<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerPullCommand extends DockerCommand{
    
    protected function configure() {
        $this
            ->setName('docker:pull')
            ->setDescription('pull docker images')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $this->initDockerCommand();
        passthru($this->getComposeString() . ' pull', $err);

        return Command::SUCCESS;
    }

    
}
