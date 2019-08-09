<?php

$version = 'be'; #tableprefix in database tine20.

return array(
    'database' => array(
        'host'          => $_ENV["TINE20_DBHOST"],
        'dbname'        => $_ENV["TINE20_DBNAME"],
        'username'      => $_ENV["TINE20_DBUSER"],
        'password'      => $_ENV["TINE20_DBPASSWD"],
        'tableprefix'   => $version,
        'adapter'       => 'pdo_mysql',
    ),

    // TODO mkdir in Dockerfile if we want to use this
    'confdfolder'       => '/tine/conf.d',

    'setupuser' => array(
        'username'      => $_ENV["TINE20_SETUPUSER"],
        'password'      => $_ENV["TINE20_SETUPPASSWD"]
    ),

   'login' => array(
       'username'      => $_ENV["TINE20_ADMINUSER"],
       'password'      => $_ENV["TINE20_ADMINPASSWD"]
    ),

    'caching' => array (
       'active' => true,
       'lifetime' => 3600,
       'backend' => 'Redis',
       'redis' => array (
           'host' => 'cache',
           'port' => 6379,
           'prefix' => 'master'
       ),
    ),

    'session' => array (
        'lifetime' => 86400,
        'backend' => 'Redis',
        'host' => 'cache',
        'port' => 6379,
        /**** TODO: add prefix here *****/
    ),

    'logger' => array (
        'active' => true,
        'filename' => 'php://stdout',
        'priority' => '5',
        'additionalWriters' => array(array(
            'active' => true,
            'filename' => '/tine/logs/debug.log',
            'priority' => '7',
            'filter'   => array(
                #'user'    => 'tine20admin',
                #'message' => '/Addressbook_Frontend_Http/',
            ),
        ))
    ),
    'filesdir'  => '/tine/files',
    'tmpdir' => '/tine/tmp',

    // needed for shared email accounts and mailinglists
    'credentialCacheSharedKey' => 't4ZzuAHA5he8',
);
