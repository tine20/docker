<?php

namespace App\Commands\Tine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\ConsoleStyle;

class TineInstallCommand extends TineCommand
{
    protected function configure()
    {
        parent::configure();

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
        parent::execute($input, $output);
        $io = new ConsoleStyle($input, $output);
        $inputOptions = $input->getArgument('modules');
        
        if(empty($inputOptions)) {
            if ($this->active('mailstack')) {
                $this->mailstackInit($io);
                $this->mailstackReset($io);
            }
    
            passthru($this->getComposeString() . ' exec -T cache sh -c "redis-cli flushall"', $result_code);
            $io->info("Installing tine ...");
            passthru($this->getComposeString() . ' exec -T web tine20_install', $result_code);
        } else {
            passthru($this->getComposeString() . ' exec --user tine20 -T web sh -c "cd tine20 && php setup.php --install "'
                . implode(" ", $inputOptions), $result_code);
        }

        if (0 !== $result_code) {
            $io->error('Install tine failed!');
            return $result_code;
        }

        if (file_exists('tine20/scripts/postInstallDocker.sh')) {
            $io->info("Running postInstallDocker.sh ... ");
            passthru($this->getComposeString()
                . ' exec -T web sh -c "/usr/share/scripts/postInstallDocker.sh"', $result_code);
        }

        if ($this->active('broadcasthub') || $this->active('broadcasthub-dev')) {
            // Key authTokenChanels needs to be set in config,
            // table tine20_auth_token will be created:
            //    'authTokenChanels' => [
            //        'records' => [
            //            'name' => 'broadcasthub'
            //        ],
            //    ],
            passthru($this->getComposeString() . ' exec --user tine20 -T web sh -c "cd tine20 && php setup.php --add_auth_token -- user=tine20admin id=longlongid auth_token=longlongtoken valid_until=' . date('Y-m-d', strtotime('+1 year', time())) . ' channels=broadcasthub"', $result_code);
        }

        return $result_code;
    }
}
