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

        $tineDir = $this->getTineDir($io);

        self::runNpmInstall($tineDir . '/Tinebase/js', $this->branch);

        return 0;
    }

    public static function runNpmInstall($dir, $branch): int
    {
        $image = DockerCommand::getImages($branch)['webpack'];
        passthru("docker run --rm \
            --user " . trim(`id -u`) . ':' . trim(`id -g`) . " \
            -v $dir:/usr/share/tine20/Tinebase/js \
            $image \
            sh -c 'cd /usr/share/tine20/Tinebase/js && npm prune --no-optional --ignore-scripts'", $result_code); // --loglevel verbose
        return $result_code;
    }
}
