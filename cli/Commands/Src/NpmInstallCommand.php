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

        $branch = $this->branch;
        if (isset($this->config['tine20']['npminstall']['branchmatrix'][$branch])) {
            $branch = $this->config['tine20']['npminstall']['branchmatrix'][$branch];
        }

        if (in_array('compose/webpack.yml', $this->composeFiles)) {
            $io->info('Running npm install ...');
            if (!isset($this->config['tine20']['npminstall']['uselink']) || !$this->config['tine20']['npminstall']['uselink']) {
                $io->info('Running npm install with strategy synced');
                if (0 !== ($result_code = $this->npmInstallSynced($io, $branch))) {
                    return $result_code;
                }
            } else {
                $io->info('Running npm install with strategy linked');
                if (0 !== ($result_code = $this->npmInstallLinked($io, $branch))) {
                    return $result_code;
                }
            }
            $io->info('npm install finished');
        }

        return 0;
    }

    // NOTE: does not work with 2023+
    protected function npmInstallLinked(ConsoleStyle $io, $branch): int
    {
        $tineDir = $this->getTineDir($io);
        $nodeModulesPath = $tineDir . '/Tinebase/js/node_modules';
        $branchFolder = $this->createBranchFolder($io, $branch, $nodeModulesPath);
        if (! $branchFolder) return 1;

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
        return $this->runNpmInstall($tineDir . '/Tinebase/js');
    }

    protected function npmInstallSynced(ConsoleStyle $io, $branch) : int
    {
        $cacheDir = $this->createBranchFolder($io, $branch, $this->baseDir . "/cache/node_modules");
        $io->info("cacheDir: $cacheDir");

        passthru("cp $this->srcDir/tine20/Tinebase/js/package.* $cacheDir && cp $this->srcDir/tine20/Tinebase/js/npm-* $cacheDir", $result_code);
        if (0 !== $result_code) {
            $io->error("can't copy dependencies definition");
            return 1;
        }

        if (0 !== ($result_code = $this->runNpmInstall($cacheDir))) {
            $io->error("npm prune failed with code $result_code");
            return $result_code;
        }

        $io->info('rsync node_modules ...');
        passthru("rsync -a --delete $cacheDir/node_modules $this->srcDir/tine20/Tinebase/js", $result_code);
        if (0 !== $result_code) {
            $io->error("can't rsync node_modules");
            return 1;
        }

        return 0;
    }

    public function createBranchFolder($io, $branch, $nodeModulesPath) : string
    {
        $branch = str_replace('/', '_', $branch);
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
                return '';
            }
            $io->info('starting with empty folder ' . $branchFolder);
        }

        return $branchFolder;
    }

    public function runNpmInstall($dir): int
    {
        $env = $this->getComposeEnv();

        $localCacheDir = trim(`npm config get cache`);

        // NOTE: we can't use getComposeCommand here as mutagen has a ro filesystem and even if we skip mutagen here
        //       it runs in the existing node container with ro filesystem (well we could kill the node-container but
        //       this tradeoff seems to big
        passthru("docker run --rm \
            --user " . trim(`id -u`) . ':' . trim(`id -g`) . " \
            -v $localCacheDir:/.npm \
            -v $dir:/usr/share/tine20/Tinebase/js \
            {$env['WEBPACK_IMAGE']} \
            sh -c 'cd /usr/share/tine20/Tinebase/js && npm prune --no-optional --ignore-scripts'", $result_code); // --loglevel verbose
        return $result_code;
    }
}
