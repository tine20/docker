<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TineAwaitDbCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:awaitdb')
            ->setDescription('executes tine20.php with command, dont use the --config option')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        passthru($this->getComposeString() . ' exec -T --user tine20 web tine20_await_db', $result_code);
        return $result_code;
    }
}