<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineTestCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:test')
            ->setDescription('starts test')
            ->setHelp('')
            ->addArgument(
                'path', 
                InputArgument::REQUIRED, 
                'the path for the tests')
            ->addOption(
                'stopOnFailure',
                's',
                InputOption::VALUE_NONE,
                'stop on failure'
            )
            ->addOption(
                'exclude',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'excludes group'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);

        $path = $input->getArgument('path');

        $this->initCompose();
        
        if ($input->getOption('stopOnFailure')) {
            $stopOnFailure = true;
        } else {
            $stopOnFailure = false;
        }

       

        ob_start();
        system(
            $this->getComposeString()
            . " exec -T --user tine20 web sh -c \"cd /usr/share/tests/tine20/ && php -d include_path=.:/etc/tine20/ /usr/share/tine20/vendor/bin/phpunit --color --debug "
            . ($stopOnFailure === true ? ' --stop-on-failure ' : '')
            . (!empty($input->getOption('exclude')) ? ' --exclude ' . implode(",", $input->getOption('exclude')) . " ": "")
            . $path
            . "\""
            . ' 2>&1'
        );


        $output = ob_get_contents();
        ob_get_clean();
        $output = strstr($output, 'There');
        
        if(empty($output))
        {
            $io->success("There were 0 errors");
        } else {
            $io->warning($output);
        } 

        
        return Command::SUCCESS;
    }

    
}

