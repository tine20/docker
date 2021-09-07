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
        $username = $io->ask('Username: ');
        $password = $io->ask('Password: ');
        $this->tineCli('--method Tinebase.createAllDemoData  --username=' . $username . ' --password='. $password);

        return Command::SUCCESS;
    }

    
}

