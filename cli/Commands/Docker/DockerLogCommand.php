<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerLogCommand extends DockerCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('docker:log')
            ->setDescription('displays logs (interactive)')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        passthru($this->getComposeString() . ' logs -f', $result_code);

        return $result_code;
    }
}