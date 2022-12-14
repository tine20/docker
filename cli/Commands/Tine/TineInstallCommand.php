<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;
use App\Commands\Tine\TineCommand;

class TineInstallCommand extends TineCommand{
    
    protected function configure() {
        $this
            ->setName('tine:install')
            ->setDescription('install tine')
            ->setHelp('')
            ->addArgument(
                'modules',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The modules you want to install'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $inputOptions = $input->getArgument('modules');
        $this->initCompose();
        
        if(empty($inputOptions)) {
            if ($this->active('mailstack')) {
                $this->mailstackInit($io);
                $this->mailstackReset($io);
            }
    
            passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $err);
            $io->info("Installing tine ...");
            $result = passthru($this->getComposeString() . ' exec --user tine20 -T web tine20_install', $err);
        } else {
            $result = passthru($this->getComposeString() . ' exec --user tine20 -T web sh -c "cd tine20 && php setup.php --install "'
            . implode(" ", $inputOptions), $err);
        }

        if ($result === false) {
            $io->error('Install tine failed!');
            return Command::FAILURE;
        }

        passthru($this->getComposeString() . ' exec -T web sh -c "test -f ${TINE20ROOT}/scripts/postInstallDocker.sh && ${TINE20ROOT}/scripts/postInstallDocker.sh"', $err);

        if ($this->active('broadcasthub') || $this->active('broadcasthub-dev')) {
            // Key authTokenChanels needs to be set in config,
            // table tine20_auth_token will be created:
            //    'authTokenChanels' => [
            //        'records' => [
            //            'name' => 'broadcasthub'
            //        ],
            //    ],
            passthru($this->getComposeString() . ' exec --user tine20 -T web sh -c "cd tine20 && php setup.php --add_auth_token -- user=tine20admin id=longlongid auth_token=longlongtoken valid_until=' . date('Y-m-d', strtotime('+1 year', time())) . ' channels=broadcasthub"');
        }

        return Command::SUCCESS;
    }
}
