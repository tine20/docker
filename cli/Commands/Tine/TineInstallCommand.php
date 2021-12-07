<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineInstallCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:install')
            ->setDescription('install tine')
            ->setHelp('')
            ->addArgument(
                'modules',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The modules you want to install'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $inputOptions = $input->getArgument('modules');
        $this->initCompose();
        
        if(empty($inputOptions)) {
            if ($this->active('mailstack')) {
                $this->mailstackInit($io);
                $this->mailstackReset($io);
            }
    
            passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $err);
            $io->notice("Installing Tine 2.0 ...");
            passthru($this->getComposeString() . ' exec -T web tine20_install', $err);
        }else {
            passthru($this->getComposeString() . ' exec -T web sh -c "cd tine20 && php setup.php --install "'
            . implode(" ", $inputOptions), $err);
        }

        return Command::SUCCESS;
    }

    
}

