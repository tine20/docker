<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\ConsoleStyle;

class TineUninstallCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('tine:uninstall')
            ->setDescription('uninstall tine')
            ->setHelp('')
            ->addArgument(
                'modules',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The modules you want to uninstall'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $io = new ConsoleStyle($input, $output);
        $inputArguments = $input->getArgument('modules');

        if(empty($inputArguments)) {
            passthru($this->getComposeString() . ' exec -T --user tine20 web sh -c "cd /usr/share/tine20 && vendor/bin/phing -D configdir=/etc/tine20 tine-uninstall"', $result_code);

            if ($this->active('mailstack')) {
                $this->mailstackReset($io);
            }
        } else {
            passthru($this->getComposeString() . ' exec -T --user tine20 web sh -c "cd /usr/share/tine20 && php setup.php --uninstall "'
            . implode(" ", $inputArguments), $result_code);
        }
        
        return $result_code;
    }
}