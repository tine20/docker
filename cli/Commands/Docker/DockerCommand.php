<?php

namespace App\Commands\Docker;

use App\Commands\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ConsoleStyle;

class DockerCommand extends BaseCommand {
    
    private static $instance;

    private $composeCommand = 'docker-compose';
    private $composeNames;
    private $composeFiles;
    private $ignoreTineConfig;
    protected $branch = 'main';

    public static $imageMap = [
        '2022.11' => [
            'web' => 'dockerregistry.metaways.net/tine20/tine20/dev:2022.11-8.0',
            'webpack' => 'node:12.22-alpine',
        ],
        '2023.11' => [
            'web' => 'dockerregistry.metaways.net/tine20/tine20/dev:2023.11-8.0',
            'webpack' => 'node:12.22-alpine',
//            'webpack' => 'node:18.9.0-alpine',
        ],
    ];

    protected function configure()
    {
        parent::configure();

        $this->addOption(
            'branch',
            'b',
            InputArgument::OPTIONAL,
            'tine branch you want to run (e.g. --branch 2023.11)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);
        
        if (is_file('pullup.json')) {
            $conf = json_decode(file_get_contents('pullup.json'), true);
        } else {
            $conf = json_decode(file_get_contents('.pullup.json'), true);
        }

        $this->initCompose($conf);
    }

    public static function getImages($branch)
    {
        $major = basename($branch );
        if (array_key_exists($major, self::$imageMap)) {
            return self::$imageMap[$major];
        } else {
            return end(self::$imageMap);
        }
    }

    public function getTineDir($io)
    {
        if (! is_file("{$this->tineDir}/tine20.php")) {
            $input = $io->choice('tine20 dir is not linked. Should it be cloned?', ['yes', 'no', 'ignore'], 'yes');
            
            switch($input) {
                case 'yes':                    
                    $output = system('git clone git@gitlab.metaways.net:tine20/tine20.git tine20 2>&1');
                    if(strpos($output, 'Cloning') === 0){
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
        return $this->tineDir;
    }

    public function getBroadcasthubDir($io)
    {

        if (($this->active('broadcasthub') || $this->active('broadcasthub-dev')) && ! is_file('broadcasthub/package.json')) {
            $input = $io->choice('broadcasthub dir is not linked. Should it be cloned and installed?', ['yes', 'no', 'ignore'], 'yes');

            switch($input) {
                case 'yes':
                    system('git clone git@gitlab.metaways.net:tine20/tine20-broadcasthub.git broadcasthub 2>&1');
                    $output = system('cd broadcasthub && npm install');

                    $io->notice($output);


                    break;

                case 'no':
                    $io->notice('link broadcasthub dir: ln -s /path/to/broadcasthub/repo broadcasthub');
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

        $this->composeCommand = in_array('mutagen', $conf['composeFiles']) ? 'mutagen-compose' : $this->composeCommand;
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

    public function anotherConfig($io) {
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

    public function getComposeString() {
        $env = '';
        foreach($this->getComposeEnv() as $k => $v) {
            $env .= "${k}=${v} ";
        }

        return $env . $this->composeCommand . ' -f ' . join(' -f ', $this->composeFiles);
    }

    public function getComposeArray(): array {
        $cmd = [$this->composeCommand];
        foreach ($this->composeFiles as $file) {
            $cmd[] = '-f';
            $cmd[] = $file;
        }
        return $cmd;
    }

    public function getComposeEnv(): array {
        $env = [];
        foreach(self::getImages($this->branch) as $service => $image) {
            $var = strtoupper($service) . '_IMAGE';
            $arch = stristr($image, 'dockerregistry.metaways.net') && stristr(`uname -a`, 'arm64') ? '-arm64' : '';

            $env[$var] = "${image}${arch}";
        }
        return $env;
    }

    public function updateConfig($updates)
    {
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
}

