<?php

namespace App\Commands\Src;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\ConsoleStyle;

use App\Commands\Docker\DockerCommand;
use App\Commands\Docker\DockerNpmInstallCommand;
use App\Commands\Docker\DockerWebpackRestartCommand;
use App\Commands\Tine\TineClearCacheCommand;

class ChangeBranchCommand extends DockerCommand {

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('src:changeBranch')
            ->setDescription('change dev branch')
            ->setHelp('')
            ->addArgument(
                'branch',
                InputArgument::REQUIRED,
                'target branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // @TODO optionally check out git code?
        // @TODO optionally update git submodules
        // @TODO start with a base? e.g. metaways/2023.11 can start with 2023.11 cache?

        // stop tine/web container to make sure DB PREFIX is correct & tine20_install is executed
        // restart/up is done in DockerWebpackRestartCommand
        $io = new ConsoleStyle($input, $output);
        $io->info('Stopping web container ...');
        $composeString = $this->getComposeString();
        passthru($composeString . " stop web");

        $io->info('Running composer install ...');
        $this->composerInstall($this->branch);

        $io->info('Running npm install ...');
        $this->npmInstall($this->branch);

        (new DockerWebpackRestartCommand())->execute($input, $output);
        (new TineClearCacheCommand())->execute($input, $output);

        return Command::SUCCESS;
    }

    // @todo: execute in php container? does it have composer?
    public function composerInstall($targetBranch)
    {
        $cacheDir = $this->baseDir . "/cache/composer/$targetBranch";
        echo "cacheDir: $cacheDir\n";
        // @TODO compute basebranch and prefill if cacheDir is empty

        `mkdir -p $cacheDir`;
        `cp $this->srcDir/tine20/composer.* $cacheDir`;
        `cd $cacheDir && composer install --ignore-platform-reqs`;

        `rsync -a --delete $cacheDir/vendor $this->srcDir/tine20`;
    }

    public function npmInstall($targetBranch)
    {
        $cacheDir = $this->baseDir . "/cache/node/$targetBranch";
        echo "cacheDir: $cacheDir\n";
        // @TODO compute basebranch and prefill if cacheDir is empty

        `mkdir -p $cacheDir`;
        `cp $this->srcDir/tine20/Tinebase/js/package.* $cacheDir`;
        `cp $this->srcDir/tine20/Tinebase/js/npm-* $cacheDir`;

        DockerNpmInstallCommand::runNpmInstall($cacheDir, $targetBranch);
        `rsync -a --delete $cacheDir/node_modules $this->srcDir/tine20/Tinebase/js`;
    }
}