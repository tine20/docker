<?php

namespace App\Commands;

use App\ConsoleStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BaseCommand extends Command
{
    /**
     * @var string base dir of console project
     */
    protected $baseDir;

    /**
     * @var string src dir of tine project
     */
    protected $srcDir;

    /**
     * @var string src dir of tine application/server code
     */
    private  $tineDir;

    /**
     * @var string src dir of tine application/server unittests
     */
    protected  $unitTestsDir;

    /**
     * @var string current branch @see --branch option
     */
    protected $branch = 'main';

    protected array $config = [];

    protected function configure()
    {
        $this->baseDir = dirname(dirname(__DIR__));
        if (is_file($this->baseDir . '/cli/config.yml')) {
            $this->config = Yaml::parseFile($this->baseDir . '/cli/config.yml');
        }
        $this->srcDir = $this->baseDir . "/tine20";
        $this->tineDir = $this->srcDir . "/tine20";
        $this->unitTestsDir = $this->srcDir . "/tests/tine20";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $this->branch = $input->hasOption('branch') && $input->getOption('branch') ?
            $input->getOption('branch') :
            trim(`cd {$this->getTineDir($io)} && git rev-parse --abbrev-ref HEAD`);
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
                    exit;

                case 'ignore':
                    $io->text('Ignore');
                    break;
            }
        }
        return $this->tineDir;
    }
}
