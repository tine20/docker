<?php

namespace App\Commands\Src;

use \Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('change dev branch in a running dev system')
            ->setHelp('')
        ;
        $this->addOption(
            'git',
            'g',
            InputOption::VALUE_NONE,
            'run git checkout BRANCH and git submodule update operations (requires --branch options)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);

        if ($input->getOption('git')) {
            if (!  $input->getOption('branch')) {
                $io->error("use --branch BRANCH to select a branch");
                return 1;
            }
            $io->info("git checkout {$this->branch}");

            passthru("cd {$this->getTineDir($io)} && git checkout {$this->branch}", $result_code);
            if (0 !== $result_code) {
                $io->error("git checkout {$this->branch} failed");
                return 1;
            }
            passthru("cd {$this->getTineDir($io)} && git submodule update", $result_code);
            if (0 !== $result_code) {
                $io->error("git submodule install");
                return 1;
            }
        }

        // stop tine/web container to make sure DB PREFIX is correct & tine20_install is executed
        // restart/up is done in DockerWebpackRestartCommand

        $io->info('Stopping web container ...');
        $composeString = $this->getComposeString();
        passthru($composeString . " stop web");

        $io->info('Running composer install ...');
        $composerCmd = new ComposerCommand();
        $a = new ArrayInput([]);
        $a->bind($composerCmd->getDefinition());
        $a->setArgument('cmd', 'install');
        ($composerCmd)->execute($a, $output);

        (new NpmInstallCommand())->execute($input, $output);
        (new DockerWebpackRestartCommand())->execute($input, $output);
        (new TineClearCacheCommand())->execute($input, $output);

        return 0;
    }
}