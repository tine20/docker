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

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $io = new ConsoleStyle($input, $output);

        $this->getTineDir($io);
        $this->getBroadcasthubDir($io);
        $this->anotherConfig($io);

        // NOTE: we need to support node version (container) change
        passthru($this->getComposeString() . " stop webpack ", $err);
        passthru($this->getComposeString() . " rm -f ", $err);
        passthru($this->getComposeString() . " up -d ", $err);

        return $err;
    }


}

