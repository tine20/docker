<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
//use Symfony\Component\Process\Process;

class DockerPullCommand extends DockerCommand
{
    
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('docker:pull')
            ->setDescription('pull docker images')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        /*
         * this needs symfony update first
         * https://github.com/symfony/symfony/issues/44821
         $process = new Process(array_merge($this->getComposeArray(), ['pull']));
        $process->setPassMode(true);

        $process->run();

        return $process->getExitCode();*/

        passthru($this->getComposeString() . ' pull', $result_code);
        return $result_code;
    }
}

