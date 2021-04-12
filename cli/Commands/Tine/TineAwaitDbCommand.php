<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineAwaitDbCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:awaitdb')
            ->setDescription('executes tine20.php with command, dont use the --config option')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $this->initCompose();
        passthru($this->getComposeString() . ' exec -T --user tine20 web tine20_await_db', $err);
        return Command::SUCCESS;
    }

    
}