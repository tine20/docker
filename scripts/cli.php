#!/usr/bin/php
<?php
function docker($args) {
    $command = 'docker-compose -f docker-compose.yml ';
    foreach ($args as $arg) {
        // strip some unwanted chars
        $composeFile = preg_replace('/[^\w^_^\s]/', '', escapeshellarg($arg));
        $filename = 'compose/' . $composeFile . '.yml';
        if (file_exists($filename)) {
            $command .= '-f ' . $filename . ' ';
        }
    }
    system($command . ' up');
}

function tineTest($args) {
    $argument = array_shift($args);
    switch ($argument) {
        case 'AllTests':
            system('docker exec --user nginx tine20 sh -c "cd /tine/tests/tine20/ && ../../tine20/vendor/bin/phpunit --color --stop-on-failure --debug AllTests"');
            break;
    }
}

function tine($args) {
    $argument = array_shift($args);
    switch (strtolower($argument)) {
        case 'install':
            # TODO allow to install with other install.properties (i.e. multiinstance)
            system('docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && vendor/bin/phing -D configdir=/tine/customers/localhost tine-install"');
            break;
        case 'uninstall':
            system('docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && vendor/bin/phing -D configdir=/tine/customers/localhost tine-uninstall"');
            break;
        case 'test':
            tineTest($args);
            break;
        case 'createdemodata':
            system('docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php --config /tine/customers/localhost/config.inc.php --method Tinebase.createAllDemoData  --username=tine20admin --password=tine20admin"');
            break;
    }
}

function mailstack($args) {
    $argument = array_shift($args);
    switch (strtolower($argument)) {
        case 'init':
            system('docker-compose -f docker-compose.yml -f compose/mailstack.yml run mailstack init');
            break;
        case 'reset':
            system('docker-compose -f docker-compose.yml -f compose/mailstack.yml run mailstack reset');
            break;
        case 'reset_multiinstance':
            system('docker-compose -f docker-compose.yml -f compose/mailstack.yml run mailstack reset_multiinstance');
            break;
        case 'build':
            system('docker-compose -f docker-compose.yml -f compose/mailstack.yml build');
            break;
    }
}


array_shift($argv);
$argument = array_shift($argv);
switch (strtolower($argument)) {
    case 'docker':
        docker($argv);
        break;
    case 'tine':
        tine($argv);
        break;
    case 'mailstack':
        mailstack($argv);
}