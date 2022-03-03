<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineCliCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:cli')
            ->setDescription('executes tine20.php with command, dont use the --config option')
            ->setHelp('')
            ->addArgument('options', InputArgument::REQUIRED, 'Options')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $this->initCompose();
        $cmd = $input->getArgument('options');
        passthru($this->getComposeString() . ' exec --user tine20 web sh -c "cd /usr/share/tine20/ && php tine20.php --config \$TINE20_CONFIG_PATH ' . $cmd . '"', $err);

        return Command::SUCCESS;
    }    
}

