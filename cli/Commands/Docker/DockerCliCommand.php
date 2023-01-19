<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;

class DockerCliCommand extends DockerCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('docker:cli')
            ->setDescription('start shell in service name eg db or web for tine20')
            ->setHelp('')
            //->addArgument('container', InputArgument::OPTIONAL, 'The name of your container')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        parent::execute($input, $output);
        
        /*$container = $input->getArgument('container');
        if(empty($container)) {*/
            ob_start();
            passthru('docker ps --format "{{.Names}}"');
            $runningContainers = preg_split("/\r\n|\n|\r/", ob_get_contents());
            ob_end_clean();

            $input = $io->choice('Select a Container', $runningContainers, '0');

            passthru('docker exec -it ' . $input . ' sh', $result_code);
/*
 * this works, but the tty forwarding somehow is bogus, the console prompt is not visible, it looks like it wouldn't work to a user -> disabled until fixed
        } else {
            echo $this->getComposeString() . ' exec ' . $container . ' sh';
            passthru($this->getComposeString() . ' exec -it ' . $container . ' sh', $result_code);

        }*/
        return $result_code;
    }
}

