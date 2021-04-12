<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
            ->addArgument('path', InputArgument::REQUIRED, 'the path for the tests')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);

        $path = $input->getArgument('path');

        $this->initCompose();
        $stopOnFailure=true;
        if (isset($path[0]) && $path[0] == "--do-not-stop-on-failure") {
            array_shift($path);
            $stopOnFailure = false;
        }
            $stopOnFailure = true;        

        $output = system(
                $this->getComposeString()
                    . " exec -T --user tine20 web sh -c \"cd /usr/share/tests/tine20/ && php -d include_path=.:/etc/tine20/ /usr/share/tine20/vendor/bin/phpunit --color --debug"
                    . ($stopOnFailure === true ? ' --stop-on-failure' : '')
                    . ' '
                    //. join(' ', $path)
                    . $path
                    . "\""
                    . '2>&1'
        );

        
        //$io->notice(var_dump($output));

        return Command::SUCCESS;
    }

    
}

