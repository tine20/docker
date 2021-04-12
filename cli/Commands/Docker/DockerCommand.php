<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;

class DockerCommand extends Command{
    
    private static $instance;

    private $composeNames = [];
    private $composeFiles;
    private $ignoreTineConfig;

    public function getTineDir($io)
    {
        if (! is_file('tine20/tine20/tine20.php')) {
            $input = $io->choice('tine20 dir is not linked. Should it be cloned?', ['yes', 'no', 'ignore'], 'yes');
            
            switch($input) {
                case 'yes':                    
                    $output = system('git clone git@gitlab.metaways.net:tine20/tine20.git tine20 2>&1');
                    if(str_starts_with($output, 'Cloning')){
                        $io->success('Tine clones succesfully');
                    }else {
                        $io->error('failed to clone Tine');
                        exit;
                    }
                    $io->success('tine20 cloned, now checkout your branch and install php and npm dependencies');
                    break;

                case 'no':
                    $io->notice('link tine20 dir: ln -s /path/to/tine/repo tine20');
                    break;

                case 'ignore':
                    $io->text('Ignore');
                    break;
            } 
        }
        
    }

    public function getDocserviceDir($io)
    {
        
        if ($this->active('docservice') && ! is_file('docservice/composer.json')) {
            $input = $io->choice('docservice dir is not linked. Should it be cloned and installed?', ['yes', 'no', 'ignore'], 'yes');

            switch($input) {
                case 'yes':
                    system('git clone git@gitlab.metaways.net:tine20/documentPreview.git docservice 2>&1');
                    $output = system('cd docservice && composer install --ignore-platform-reqs');

                    $io->notice($output);
                    
                    
                    break;

                case 'no':
                    $io->notice('link docservice dir: ln -s /path/to/docservice/repo docservice');
                    break;

                case 'ignore':
                    break;
            }
        }
    }

    public function active($composeName) {
        return in_array($composeName, $this->composeNames);
    }

    public function initCompose() {
        if (is_file('pullup.json')) {
            $conf = json_decode(file_get_contents('pullup.json'), true);
        } else {
            $conf = json_decode(file_get_contents('.pullup.json'), true);
        }

        $this->composeFiles = ['docker-compose.yml'];
        $this->composeNames = [];
        $this->ignoreTineConfig = array_key_exists('ignoreConfig', $conf) and $conf['ignoreConfig'];

        if (array_key_exists('composeFiles', $conf)) {
            foreach ($conf['composeFiles'] as $compose) {
                $filename = 'compose/' . $compose . '.yml';
                if (file_exists($filename)) {
                    $this->composeNames[] = $compose;
                    $this->composeFiles[] = $filename;
                } else {
                    echo "$compose unknown";
                }
            }
        }
    }

    public function anotherConfig() {
        if (is_file('tine20/tine20/config.inc.php')) {
            if ($this->ignoreTineConfig) {
                return 0;
            }

            $input = $io->choice('found a config.inc.php in your tine dir, this could cause trouble. Should it be removed?', ['yes', 'no', 'ignore']);

            switch($input) {
                case 'yes':
                    unlink('tine20/tine20/config.inc.php');
                    $io->success('config.inc.php removed');
                    break;
                
                case 'no':
                    exit(1);
                    break;

                case 'ignore':
                    $this->updateConfig(['ignoreConfig' => true]);
                    break;
            }
        }
    }

    private function updateConfig($updates) {
        if (is_file('pullup.json')) {
            $conf = json_decode(file_get_contents('pullup.json'), true);
        } else {
            $conf = json_decode(file_get_contents('.pullup.json'), true);
        }

        $conf = array_merge($conf, $updates);

        $f = fopen('pullup.json', 'w+');
        fwrite($f, json_encode($conf, JSON_PRETTY_PRINT));
        fclose($f);

        $this->initCompose($conf);
    }
    
    public function getComposeString() {
        return 'docker-compose -f ' . join(' -f ', $this->composeFiles);
    }
}

