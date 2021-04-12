<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineUninstallCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:uninstall')
            ->setDescription('uninstall tine')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        
        $this->initCompose();
        passthru($this->getComposeString() . ' exec -T --user tine20 web sh -c "cd /usr/share/tine20/ && vendor/bin/phing -D configdir=/etc/tine20 tine-uninstall"', $err);

        if ($this->active('mailstack')) {
            $this->mailstackReset($io);
        }

        return Command::SUCCESS;
    }

    
}

