<?php

namespace App\Commands\Src;

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

        $localCacheDir = trim(`composer config cache-dir`);

        `mkdir -p $this->baseDir/data/composer`;

        passthru($this->getComposeString() . ' run --rm --user ' . trim(`id -u`) . ':' . trim(`id -g`) . ' -v ' .
            $this->baseDir . '/data/composer:/.composer -v ' . $localCacheDir .
            ':/composercache web sh -c "cd /usr/share/tine20; composer config --global cache-dir /composercache; composer ' . $input->getArgument('cmd') . '"', $result_code);

        return $result_code;
    }
}
