<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineDemodataCommand extends TineCommand{
    
    protected function configure() {
        parent::configure();

        $this
            ->setName('tine:demodata')
            ->setDescription('creates demodata')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);

        $this->initCompose();

        $this->tineCli('--method Tinebase.createAllDemoData  --username=\$TINE20_LOGIN_USERNAME --password=\$TINE20_LOGIN_PASSWORD');

        return Command::SUCCESS;
    }


}

