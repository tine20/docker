<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerUpCommand extends DockerCommand{
    
    protected function configure() {
        $this
            ->setName('docker:up')
            ->setDescription('start docker setup.  pulls/builds images, creates containers, starts containers and shows logs')
            ->setHelp('')
            ->addArgument(
                'container',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'names of additional containers'
            )
            ->addOption(
                'detached',
                'd',
                InputOption::VALUE_NONE,
                'set detached mode'
            )
            ->addOption(
                'default',
                'D',
                InputOption::VALUE_NONE,
                'start docker with the default containers'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initDockerCommand();
        $io = new ConsoleStyle($input, $output);
        $inputOptions = $input->getArgument('container');

        if($input->getOptions('default') && is_file('pullup.json')) {
            unlink('pullup.json');
        }

        $this->getTineDir($io);
        $this->getDocserviceDir($io);
        $this->anotherConfig($io);
        
        if(!empty($inputOptions)) {
            $this->updateConfig(['composeFiles' => $inputOptions]);
        }else if($input->getOption('default') === true && is_file('pullup.json')){
            unlink('pullup.json');
        }
        
        passthru($this->getComposeString() . ' up' .
        ($input->getOption('detached') === true ? ' -d' : ''), $err);
        return Command::SUCCESS; 
    }
}

