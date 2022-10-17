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
            ->addArgument('container', InputArgument::OPTIONAL, 'The name of your container')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $this->initDockerCommand();
        
        $container = $input->getArgument('container');
        if(empty($container)) {
            ob_start();
            passthru('docker ps --format "{{.Names}}"');
            $runningContainers = preg_split("/\r\n|\n|\r/", ob_get_contents());
            ob_end_clean();

            $input = $io->choice('Select a Container', $runningContainers, '0');
            
            passthru('docker exec -it ' . $input . ' sh', $err);
            
        } else {
            passthru($this->getComposeString() . ' exec ' . $container . ' sh', $err);

        }         
        return $err;
    }

    
}

