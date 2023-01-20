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

        // NOTE: we need to support node version (container) change
        passthru($this->getComposeString() . " stop webpack", $result_code);
        passthru($this->getComposeString() . " rm -f", $result_code);

        $io->info('Restarting containers ...');

        passthru($this->getComposeString() . " up -d", $result_code);
        $webContainerId = trim(`{$this->getComposeString()} ps -q web`);

        $tries = 0;
        // give web time to fail
        sleep(1);
        //                                 check if container id is running
        while (++$tries < 20 && empty(trim(`docker ps -q --no-trunc | grep $webContainerId`))) {
            $io->info('waiting for web service to be available...');
            // give webpack time to finish compiling
            sleep(2);
            passthru($this->getComposeString() . " up -d", $result_code);
            // give web time to fail
            sleep(1);
        }

        return $tries < 20 ? 0 : 1;
    }
}
