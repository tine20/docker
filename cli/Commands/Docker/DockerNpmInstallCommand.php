<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class DockerNpmInstallCommand extends DockerCommand{

    protected function configure() {
        $this
            ->setName('docker:npminstall')
            ->setDescription('install npm dependencies')
            ->setHelp('')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);

        $this->getTineDir($io);
        $this->getBroadcasthubDir($io);
        $this->anotherConfig($io);

        self::runNpmInstall(dirname(dirname(dirname(__DIR__))).'/tine20/tine20/Tinebase/js', $this->branch);
        return Command::SUCCESS;
    }

    public static function runNpmInstall($dir, $branch)
    {
        $image = DockerCommand::getImages($branch)['webpack'];
        passthru("docker run --rm \
            -v $dir:/usr/share/tine20/Tinebase/js \
            $image \
            sh -c 'apk --no-cache add git && cd /usr/share/tine20/Tinebase/js && npm install --no-optional'"); // --loglevel verbose
    }
}
