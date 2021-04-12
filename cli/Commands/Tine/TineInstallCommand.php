<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineInstallCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:install')
            ->setDescription('install tine')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $this->initCompose();
        if ($this->active('mailstack')) {
            $this->mailstackInit($io);
            $this->mailstackReset($io);
        }

        passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $err);
        $io->notice("Installing Tine 2.0 ...");
        passthru($this->getComposeString() . ' exec -T --user tine20 web tine20_install', $err);

        return Command::SUCCESS;
    }

    
}

