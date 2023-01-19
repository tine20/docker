<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;

class DockerWebpackRestartCommand extends DockerCommand
{

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
        passthru($this->getComposeString() . " stop webpack ", $result_code);
        passthru($this->getComposeString() . " rm -f ", $result_code);

        $io->info('Restarting containers ...');

        passthru($this->getComposeString() . " up -d ", $result_code);

        return $result_code;
    }
}
