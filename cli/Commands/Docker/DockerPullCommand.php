<?php

namespace App\Commands\Docker;

use App\ConsoleStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
        ($process = new Process(array_merge($this->getComposeArray(), ['pull'])))->setTimeout(3600);

        $process->run();

        if (!empty($out = $process->getOutput())) {
            $io->writeln($out);
        }
        if (!empty($out = $process->getErrorOutput())) {
            $io->writeln($out);
        }

        return $process->getExitCode();
    }
}

