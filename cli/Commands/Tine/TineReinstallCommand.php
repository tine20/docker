<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;

class TineReinstallCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:reinstall')
            ->setDescription('reinstall tine')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $io = new ConsoleStyle($input, $output);

        $io->notice("Uninstalling Tine 2.0...");
        passthru($this->getComposeString() . ' exec -T web sh -c "cd /usr/share/tine20/ && vendor/bin/phing -D configdir=/etc/tine20 tine-uninstall"', $result_code);

        if ($this->active('mailstack')) {
            $this->mailstackReset($io);
        }

        passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $result_code);
        $io->notice("Installing Tine 2.0 ...");
        passthru($this->getComposeString() . ' exec -T web tine20_install', $result_code);
        passthru($this->getComposeString() . ' exec -T web sh -c "test -f ${TINE20ROOT}/scripts/postInstallDocker.sh && ${TINE20ROOT}/scripts/postInstallDocker.sh"', $result_code);

        return 0;
    }
}