<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;

class DockerStartCommand extends DockerCommand
{
    protected function configure() {
        $this
            ->setName('docker:start')
            ->setDescription('start docker setup.  pulls/builds images, creates containers, starts containers')
            ->setHelp('')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);

        $this->getTineDir($io);
        $this->getBroadcasthubDir($io);
        $this->anotherConfig($io);

        passthru($this->getComposeString() . ' up -d', $result_code);
        
        return $result_code;
    }

    
}

