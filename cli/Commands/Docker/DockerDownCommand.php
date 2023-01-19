<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerDownCommand extends DockerCommand
{
    protected function configure()
    {
        parent::configure();
        
        $this
            ->setName('docker:down')
            ->setDescription('destroy docker setup.  stop containers, remove containers and networks, volumes will persist')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        passthru($this->getComposeString() . ' down', $result_code);

        return $result_code;
    }
}

