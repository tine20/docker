<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineReinstallCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:reinstall')
            ->setDescription('reinstall tine')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $io->notice("Uninstalling Tine 2.0...");
        $this->initCompose();
        passthru($this->getComposeString() . ' exec -T web sh -c "cd /usr/share/tine20/ && vendor/bin/phing -D configdir=/etc/tine20 tine-uninstall"', $err);

        if ($this->active('mailstack')) {
            $this->mailstackReset($io);
        }

        passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $err);
        $io->notice("Installing Tine 2.0 ...");
        passthru($this->getComposeString() . ' exec -T web tine20_install', $err);

        return Command::SUCCESS;
    }

    
}

