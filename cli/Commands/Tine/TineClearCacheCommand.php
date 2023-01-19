<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TineClearCacheCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:clearcache')
            ->setDescription('clears all caches')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        passthru(
            $this->getComposeString()
            . " exec -T --user tine20 web sh -c \"cd /usr/share/tine20/ && php setup.php --clear_cache -v "
            . " && rm -rf /var/lib/tine20/tmp/* "
            . " && rm -rf /var/lib/tine20/caching/* "
            . ' 2>&1 "', $result_code
        );

        return $result_code;
    }
}