<?php

namespace App\Commands\Src;

use App\ConsoleStyle;
use App\Commands\Docker\DockerCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommand extends DockerCommand
{
    protected function configure()
    {
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

        $env = $this->getComposeEnv();

        // NOTE: we can't use getComposeCommand here as mutagen has a ro filesystem and even if we skip mutagen here
        //       it runs in the existing web container with ro filesystem (well we could kill the web-container but
        //       this tradeoff seems to big
        passthru('docker run --rm --user ' . trim(`id -u`) . ':' . trim(`id -g`) .
            ' -v ' . $this->getTineDir($io) . ':/usr/share/tine20' .
            ' -v ' . $this->baseDir . '/data/composer:/.composer' .
            ' -v ' . $localCacheDir . ':/composercache' .
            ' '. $env['WEB_IMAGE'] . ' sh -c "cd /usr/share/tine20; composer config --global cache-dir /composercache; composer ' . $input->getArgument('cmd') . '"', $result_code);

        return $result_code;
    }
}
