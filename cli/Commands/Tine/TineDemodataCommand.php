<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TineDemodataCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:demodata')
            ->setDescription('creates demodata')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        return $this->tineCli('--method Tinebase.createAllDemoData  --username=\$TINE20_LOGIN_USERNAME --password=\$TINE20_LOGIN_PASSWORD');
    }
}