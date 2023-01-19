<?php

namespace App\Commands\Src;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;

class NpmInstallCommand extends DockerCommand
{
    protected function configure() {
        $this
            ->setName('src:npminstall')
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

        self::runNpmInstall(dirname(dirname(dirname(__DIR__))) . '/tine20/tine20/Tinebase/js', $this->branch);
        return 0;
    }

    public static function runNpmInstall($dir, $branch)
    {
        $image = DockerCommand::getImages($branch)['webpack'];
        passthru("docker run --rm \
            -v $dir:/usr/share/tine20/Tinebase/js \
            $image \
            sh -c 'cd /usr/share/tine20/Tinebase/js && npm install --no-optional --ignore-scripts'"); // --loglevel verbose apk --no-cache add git &&
    }
}
