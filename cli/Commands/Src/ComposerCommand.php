<?php

namespace App\Commands\Src;

use App\Commands\Docker\DockerCommand;
use App\ConsoleStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ComposerCommand extends DockerCommand
{
    protected function configure() {
        $this
            ->setName('src:composer')
            ->setDescription('execute composer in tine20 src context')
            ->setHelp('')
            ->addArgument(
                'cmd',
                InputArgument::REQUIRED,
                'composer cmd to execute, like "require metaways/timezoneconverter"')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);

        $localCacheDir = trim(`composer config cache-dir`);

        `mkdir -p $this->baseDir/data/composer`;

        ($process = new Process(array_merge($this->getComposeArray(), ['run', '--rm', '--user', trim(`id -u`) . ':' . trim(`id -g`), '-v', $this->baseDir . '/data/composer:/.composer', '-v', $localCacheDir . ':/composercache', 'web', 'sh', '-c', 'cd /usr/share/tine20; composer config --global cache-dir /composercache; composer ' . $input->getArgument('cmd')])))
            ->setEnv($this->getComposeEnv())
            ->setTimeout(300);

        $io->writeln($process->getCommandLine());
        $process->run();

        if (!empty($out = $process->getOutput())) {
            $io->writeln($out);
        }
        if (!empty($out = $process->getErrorOutput())) {
            $io->writeln($out);
        }

        return $process->getExitCode();
    }
}
