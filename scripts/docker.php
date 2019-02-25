<?php
array_shift($argv);
$command = 'docker-compose -f docker-compose.yml ';
foreach ($argv as $arg) {
    switch ($arg):
        case('clamav'):
            $command .= '-f compose/clamav.yml ';
            break;
        case('confroom'):
            $command .= '-f compose/confroom.yml ';
            break;
        case('docservice'):
            $command .= '-f compose/docservice.yml ';
            break;
        case('mail'):
            $command .= '-f compose/mail.yml ';
            break;
        case('pma'):
            $command .= '-f compose/pma.yml ';
            break;
        case('webpack'):
            $command .= '-f compose/webpack.yml ';
            break;
        case('worker'):
            $command .= '-f compose/worker.yml ';
            break;
        case('xdebug'):
            $command .= '-f compose/xdebug.yml ';
            break;
    endswitch;
}
system($command . ' up');
?>