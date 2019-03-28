<?php
array_shift($argv);
$command = 'docker-compose -f docker-compose.yml ';
foreach ($argv as $arg) {
    // strip some unwanted chars
    $composeFile = preg_replace('/[^\w^_^\s]/', '', escapeshellarg($arg));
    $filename = 'compose/' . $composeFile . '.yml';
    if (file_exists($filename)) {
        $command .= '-f ' . $filename . ' ';
    }
}
system($command . ' up');
