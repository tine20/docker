<?php

return array(
    'actionqueue' => array(
        'active' => true,
        'backend' => 'Redis',
        'host' => 'cache',
        'port' => 6379,
        'queueName' => 'actionqueueName'
    ),
);