<?php

namespace App\Commands\Tine;

use App\Commands\Docker\DockerCommand;

class TineCommand extends DockerCommand
{
    public function mailstackInit($io) {
        $out = system($this->getComposeString() . ' run --rm mailstack init');

        if ('' == $out) {
            $io->success("mailstack init successful");
            return 0;
        }
        return 1;
    }

    public function mailstackReset($io) {
        $out = system($this->getComposeString() . ' run --rm mailstack reset');

        if ('' == $out) {
            $io->success("mailstack reset successful");
            return 0;
        }
        return 1;
    }

    public function setupCli($cmd) {
        passthru($this->getComposeString() . ' exec --user tine20 web sh -c "cd /usr/share/tine20/ && php setup.php ' . $cmd . '"', $result_code);

        return $result_code;
    }

    public function tineCli($cmd) {
        //--config \$TINE20_CONFIG_PATH
        passthru($this->getComposeString() . ' exec --user tine20 web sh -c "cd /usr/share/tine20/ && php tine20.php ' . $cmd . '"', $result_code);

        return $result_code;
    }
}

