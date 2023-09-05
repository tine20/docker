<?php

namespace App\Commands\Docker;

use App\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class DockerCommand extends BaseCommand
{
    protected $composeNames;
    protected $composeFiles;
    protected $ignoreTineConfig;
    protected $tablePrefix = null;
    protected $homeDir = null;
    protected array $composeCommand = ['docker', 'compose'];
    protected string $bchub_repo = 'https://github.com/tine-groupware/broadcasthub.git';

    protected static $imageMap = [
        '2021.11' => [
            'web' => 'dockerregistry.metaways.net/tine20/tine20/dev:2021.11-7.4',
            'webpack' => 'dockerregistry.metaways.net/tine20/tine20/node:2022.11', // should be the same setup
        ],
        '2022.11' => [
            'web' => 'dockerregistry.metaways.net/tine20/tine20/dev:2022.11-8.0',
            'webpack' => 'dockerregistry.metaways.net/tine20/tine20/node:2022.11',
        ],
        // TODO remove old main (repo https://github.com/tine20/tine20)
//        'main' => [
//            'web' => 'tinegroupware/dev:2022.11-8.0',
//            'webpack' => 'node:12.22-alpine',
//        ],
        '2023.11' => [
            'web' => 'dockerregistry.metaways.net/tine20/tine20/dev:2023.11-8.1',
            'webpack' => 'dockerregistry.metaways.net/tine20/tine20/node:2023.11',
        ],
        // repo https://github.com/tine-groupware/tine
        'main' => [
            'web' => 'tinegroupware/dev:2023.11-8.1',
            'webpack' => 'node:18.9.0-alpine',
        ],
    ];

    protected function configure()
    {
        parent::configure();

        if (isset($this->config['tine20']['tableprefix']) && strlen($this->config['tine20']['tableprefix']) < 9) {
            $this->tablePrefix = $this->config['tine20']['tableprefix'];
        }

        $this->addOption(
            'branch',
            'b',
            InputArgument::OPTIONAL,
            'tine branch you want to run (e.g. --branch 2023.11)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);

        $this->initCompose();
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

    public function getBroadcasthubDir($io)
    {
        if (($this->active('broadcasthub') || $this->active('broadcasthub-dev')) && ! is_file('broadcasthub/package.json')) {
            $input = $io->choice('broadcasthub dir is not linked. Should it be cloned and installed?', ['yes', 'no', 'ignore'], 'yes');

            switch($input) {
                case 'yes':
                    system('git clone ' . $this->bchub_repo . ' broadcasthub 2>&1');
                    $output = system('cd broadcasthub && npm install');

                    $io->notice($output);
                    break;

                case 'no':
                    $io->notice('link broadcasthub dir: ln -s /path/to/broadcasthub/repo broadcasthub');
                    exit;

                case 'ignore':
                    break;
            }
        }
    }

    public function active($composeName) {
        return in_array($composeName, $this->composeNames);
    }

    public function initCompose() {
        $conf = $this->getComposeConf();
        $this->composeCommand = in_array('mutagen', $conf['composeFiles']) ? ['mutagen-compose'] : $this->composeCommand;
        $this->composeFiles = ['docker-compose.yml'];
        if (file_exists('.env')) {
            $this->composeFiles[] = 'compose/env.yml';
        }
        $this->composeNames = [];
        $this->ignoreTineConfig = array_key_exists('ignoreConfig', $conf) && $conf['ignoreConfig'];

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

        $this->getComposeEnv();
    }

    public function anotherConfig($io) {
        if (is_file($this->getTineDir($io) . '/config.inc.php')) {
            if ($this->ignoreTineConfig) {
                return;
            }

            $input = $io->choice('found a config.inc.php in your tine dir, this could cause trouble. Should it be removed?', ['yes', 'no', 'ignore']);

            switch($input) {
                case 'yes':
                    unlink($this->getTineDir($io) . '/config.inc.php');
                    $io->success('config.inc.php removed');
                    break;
                
                case 'no':
                    exit(1);

                case 'ignore':
                    $this->updateConfig(['ignoreConfig' => true]);
                    break;
            }
        }
    }

    public function getComposeString() {
        $env = '';
        foreach ($this->getComposeEnv() as $k => $v) {
            $env .= "{$k}={$v} ";
        }

        return $env . join(' ', $this->composeCommand) . ' -f ' . join(' -f ', $this->composeFiles);
    }

    public function getComposeArray(): array {
        $cmd = $this->composeCommand;
        foreach ($this->composeFiles as $file) {
            $cmd[] = '-f';
            $cmd[] = $file;
        }
        return $cmd;
    }

    public function getComposeEnv(): array {
        static $env = [];
        if (empty($env)) {
            foreach (self::getImages($this->branch) as $service => $image) {
                $var = strtoupper($service) . '_IMAGE';
                $arch = stristr($image, 'dockerregistry.metaways.net') && stristr(`uname -a`, 'arm64') ? '-arm64' : '';

                $env[$var] = "{$image}{$arch}";
            }
            $homeDir = rtrim(trim(`realpath ~/`), '/');
            if (empty($homeDir) || !preg_match('#^/..+#', $homeDir)) {
                exit('home dir discovery failed');
            }
            `touch $homeDir/.dockertine20web_bash_history`;
            `touch $homeDir/.dockertine20web_ash_history`;
            $this->homeDir = $homeDir;
            $env['HOMEDIR'] = $homeDir;

            $env['IMAGE_SUFFIX'] = stristr(`uname -a`, 'arm64') ? '-arm64' : '';
            $env['TINE20_DATABASE_TABLEPREFIX'] = $this->_getTablePrefix();
        }
        return $env;
    }

    protected function _getTablePrefix(): string
    {
        return $this->tablePrefix ?? substr(str_replace(['.', '/'], '', $this->branch),0,7) . '_';
    }

    public function updateConfig($updates)
    {
        $conf = array_merge($this->getComposeConf(), $updates);
        $f = fopen($this->baseDir . '/pullup.json', 'w+');
        fwrite($f, json_encode($conf, JSON_PRETTY_PRINT));
        fclose($f);

        $this->initCompose();
    }

    public function getComposeConf(): array
    {
        if (is_file($this->baseDir . '/pullup.json')) {
            $conf = json_decode(file_get_contents($this->baseDir . '/pullup.json'), true);
        } else {
            $conf = json_decode(file_get_contents($this->baseDir . '/.pullup.json'), true);
        }

        return $conf;
    }
}
