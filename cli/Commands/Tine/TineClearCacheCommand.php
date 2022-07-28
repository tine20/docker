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

class TineClearCacheCommand extends TineCommand{

    protected function configure() {
        $this
            ->setName('tine:clearcache')
            ->setDescription('clears all caches')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $this->initCompose();


        system(
            $this->getComposeString()
            . " exec -T --user tine20 web sh -c \"cd /usr/share/tine20/ && php setup.php --clear_cache -v "
            . " && rm -Rf /var/lib/tine20/tmp/* "
            . " && rm -Rf /var/lib/tine20/caching/* "
            . ' 2>&1 "', $result_code
        );

        return $result_code === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
