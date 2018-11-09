<?php return array(
    'database' => array(
        'host'          => $_ENV["TINE20_DBHOST"],
        'dbname'        => $_ENV["TINE20_DBNAME"],
        'username'      => $_ENV["TINE20_DBUSER"],
        'password'      => $_ENV["TINE20_DBPASSWD"],
        'tableprefix'   => 'local',
        'adapter'       => 'pdo_mysql',
    ),
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
        #'filename' => '/tine/logs/tine20.log',
        'filename' => 'php://stdout',
        'priority' => '5',
    ),
    'filesdir'  => '/tine/files',
    'tmpdir' => '/tine/tmp',
);
