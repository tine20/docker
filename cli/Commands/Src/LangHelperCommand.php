<?php

namespace App\Commands\Src;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class LangHelperCommand extends DockerCommand
{
    protected function configure() {
        $this
            ->setName('src:langHelper')
            ->setDescription('run langHelper command')
            ->setHelp('')
            ->addArgument(
                'cmd',
                InputArgument::REQUIRED,
                'langHelper cmd to execute, like "-- \'-u --app Calendar\'"')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);

        $env = $this->getComposeEnv();

        // NOTE: we can't use getComposeCommand here as mutagen has a ro filesystem and even if we skip mutagen here
        //       it runs in the existing web container with ro filesystem (well we could kill the web-container but
        //       this tradeoff seems to big
        passthru('docker run --rm --user ' . trim(`id -u`) . ':' . trim(`id -g`) .
            ' -v ' . $this->getTineDir($io) . ':/usr/share/tine20' .
            ' '. $env['WEB_IMAGE'] . ' sh -c "cd /usr/share/tine20; ./langHelper.php ' . $input->getArgument('cmd') . '"', $result_code);

        return $result_code;
    }
}
