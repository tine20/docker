<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\ConsoleStyle;

class TineTestCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:test')
            ->setDescription('starts test')
            ->addUsage('AllTests')
            ->addUsage('Addressbook/Frontend/JsonTest -f testGetAllContacts')
            ->setHelp('')
            ->addArgument(
                'path', 
                InputArgument::REQUIRED | InputArgument::IS_ARRAY, 
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
            ->addOption(
                'filter',
                'f',
                InputOption::VALUE_REQUIRED,
                'sets filters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);
        $paths = $input->getArgument('path');
        
        if ($input->getOption('stopOnFailure')) {
            $stopOnFailure = true;
        } else {
            $stopOnFailure = false;
        }

        foreach ($paths as $path) {
            $filter = null;
            if (strpos($path, "::")) {
                [$path, $filter] = explode("::", $path);    
            } else if (!empty($input->getOption('filter'))) {
                $filter = $input->getOption('filter');
            }

            passthru(
                $this->getComposeString()
                . " exec -T --user tine20 web sh -c \"cd /usr/share/tests/tine20/ && php -d include_path=.:/etc/tine20/ /usr/share/tine20/vendor/bin/phpunit --color --debug "
                . ($stopOnFailure === true ? ' --stop-on-failure ' : '')
                . (!empty($input->getOption('exclude')) ? ' --exclude ' . implode(",", $input->getOption('exclude')) . " ": "")
                . ($filter ? ' --filter ' . $filter . " ": "")
                . $path
                . "\""
                . ' 2>&1', $result_code
            );
        }
        
        
        if ($result_code === 0) {
            $io->success("There were 0 errors");
        } else {
            $io->error('TESTS FAILED');
        }

        return $result_code;
    }
}
