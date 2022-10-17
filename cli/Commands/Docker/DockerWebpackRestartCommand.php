<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerWebpackRestartCommand extends DockerCommand{

    protected function configure() {
        $this
            ->setName('docker:webpackrestart')
            ->setDescription('restart webpack. needed after large js changes e.g. other apps')
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
            " exec -T web sh -c \"supervisorctl restart webpack\" ", $err);

        return $err;
    }


}

