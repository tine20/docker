<?php

namespace App\Commands;

use App\ConsoleStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;

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
    protected  $tineDir;

    /**
     * @var string src dir of tine application/server unittests
     */
    protected  $unitTestsDir;

    /**
     * @var string current branch @see --branch option
     */
    protected $branch = 'main';

    protected array $composeCommand;

    protected function configure()
    {
        $this->baseDir = dirname(dirname(__DIR__));
        $this->srcDir = $this->baseDir . "/tine20";
        $this->tineDir = $this->srcDir . "/tine20";
        $this->unitTestsDir = $this->srcDir . "/tests/tine20";
        $this->composeCommand = $this->_getComposeCommand();
    }

    protected function _getComposeCommand(): array
    {
        return ['docker', 'compose'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleStyle($input, $output);
        $this->branch = $input->hasOption('branch') && $input->getOption('branch') ?
            $input->getOption('branch') :
            trim(`cd {$this->getTineDir($io)} && git rev-parse --abbrev-ref HEAD`);
    }
}
