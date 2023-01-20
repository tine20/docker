<?php

namespace App\Commands\Src;

use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Commands\Docker\DockerCommand;
use App\Commands\Docker\DockerWebpackRestartCommand;
use App\Commands\Tine\TineClearCacheCommand;
use App\ConsoleStyle;

class ChangeBranchCommand extends DockerCommand {

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('src:changeBranch')
            ->setDescription('change dev branch')
            ->setHelp('')
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
        $composerCmd = new ComposerCommand();
        $input->bind($composerCmd->getDefinition());
        $input->setArgument('cmd', 'install');
        ($composerCmd)->execute($input, $output);

        if (in_array('compose/webpack.yml', $this->composeFiles)) {
            $io->info('Running npm install ...');
            if (!isset($this->config['tine20']['npminstall']['uselink']) || !$this->config['tine20']['npminstall']['uselink']) {
                $this->npmInstall($this->branch);
            } else {
                if (0 !== ($result_code = $this->npmInstallLinked($io))) {
                    return $result_code;
                }
            }
        }

        (new DockerWebpackRestartCommand())->execute($input, $output);
        (new TineClearCacheCommand())->execute($input, $output);

        return 0;
    }

    protected function npmInstallLinked(ConsoleStyle $io): int
    {
        $branch = $this->branch;
        if (isset($this->config['tine20']['npminstall']['branchmatrix'][$branch])) {
            $branch = $this->config['tine20']['npminstall']['branchmatrix'][$branch];
        }
        $branch = str_replace('/', '_', $branch);
        $tineDir = $this->getTineDir($io);
        $nodeModulesPath = $tineDir . '/Tinebase/js/node_modules';
        $branchFolder = $nodeModulesPath . '_' . $branch;
        if (!is_dir($branchFolder) && preg_match('/(20\d\d)\.11/', $branch, $matches)) {
            $year = intval($matches[1]);
            do {
                $path = $nodeModulesPath . '_' . $year . '.11';
                if (is_dir($path)) {
                    $io->info('initializing ' . $branchFolder . ' with ' . $path);
                    `cp -r $path $branchFolder`;
                    break;
                }
            } while (--$year >= 2022);
        }
        clearstatcache();
        if (!is_dir($branchFolder)) {
            passthru('mkdir ' . $branchFolder, $result_code);
            if (0 !== $result_code) {
                $io->error('creating folder ' . $branchFolder . ' failed');
                return 1;
            }
            $io->info('starting with empty folder ' . $branchFolder);
        }
        if (file_exists($nodeModulesPath)) {
            passthru('rm -rf ' . $nodeModulesPath, $result_code);
            if (0 !== $result_code) {
                $io->error('deleting node_modules failed');
                return 1;
            }
        }
        passthru('ln -s ./' . basename($branchFolder) . ' ' . $nodeModulesPath, $result_code);
        if (0 !== $result_code) {
            $io->error('linking node_modules failed');
            return 1;
        }
        return NpmInstallCommand::runNpmInstall($tineDir . '/Tinebase/js', $this->branch);
    }

    protected function npmInstall($targetBranch)
    {
        $cacheDir = $this->baseDir . "/cache/node/$targetBranch";
        echo "cacheDir: $cacheDir\n";
        // @TODO compute basebranch and prefill if cacheDir is empty

        `mkdir -p $cacheDir`;
        `cp $this->srcDir/tine20/Tinebase/js/package.* $cacheDir`;
        `cp $this->srcDir/tine20/Tinebase/js/npm-* $cacheDir`;

        NpmInstallCommand::runNpmInstall($cacheDir, $targetBranch);
        `rsync -a --delete $cacheDir/node_modules $this->srcDir/tine20/Tinebase/js`;
    }
}