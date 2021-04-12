<?php

namespace App\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Core {

    

    public function getTineDir($io)
    {
        if (! is_file('tine20/tine20/tine20.php')) {
            $input = $io->choice('tine20 dir is not linked. Should it be cloned?', ['yes', 'no', 'ignore']);
            
            switch($input) {
                case 'yes':                    
                    passthru('git clone http://gerrit.tine20.com/customers/tine20.com tine20', $err);
                    
                    //Better: specific error message
                    if($err == 128) {
                        $io->error('failed to clone tine20');
                        break;
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
}