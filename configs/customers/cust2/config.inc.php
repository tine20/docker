<?php return array(
    'database' => array(
        'host'          => $_ENV["TINE20_DBHOST"],
        'dbname'        => $_ENV["TINE20_DBNAME"],
        'username'      => $_ENV["TINE20_DBUSER"],
        'password'      => $_ENV["TINE20_DBPASSWD"],
        'tableprefix'   => 'cust2_',
        'adapter'       => 'pdo_mysql',
    ),
//    'statusInfo'        => true,
    'confdfolder'       => '/tine/customers/cust2/conf.d',
    'setupuser' => array(
        'username'      => $_ENV["TINE20_SETUPUSER"],
        'password'      => $_ENV["TINE20_SETUPPASSWD"]
    ),

//   'login' => array(
//       'username'      => $_ENV["TINE20_ADMINUSER"],
//       'password'      => $_ENV["TINE20_ADMINPASSWD"]
//    ),


    'logger' => array (
        'active' => true,
        #'filename' => '/tine/logs/tine20.log',
        'filename' => 'php://stdout',
        'priority' => '5',
    ),
    'filesdir'  => '/tine/files',
    'tmpdir' => '/tine/tmp',
);
