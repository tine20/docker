<?php

namespace App;

use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleStyle extends SymfonyStyle {
    
    public function notice($message)
    {
        $this->block($message, 'Notice', 'fg=black;bg=#ebb134', ' ', true);
    }

}