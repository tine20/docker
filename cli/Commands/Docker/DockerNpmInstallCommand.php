<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerNpmInstallCommand extends DockerCommand{

    protected function configure() {
        $this
            ->setName('docker:npminstall')
            ->setDescription('install npm dependencies')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initDockerCommand();
        $io = new ConsoleStyle($input, $output);

        $this->getTineDir($io);
        $this->getBroadcasthubDir($io);
        $this->anotherConfig($io);

        passthru($this->getComposeString() .
            " exec -T webpack sh -c \"npm --prefix /usr/share/tine20/Tinebase/js/ install\" ", $err);

        return Command::SUCCESS;
    }


}

