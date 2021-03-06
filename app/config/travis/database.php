<?php

return [


    'fetch'      => PDO::FETCH_CLASS,

    'default'    => 'mysql',

    'connections' => [
        'mysql'      => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'firefly',
            'username'  => 'travis',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],

    'migrations' => 'migrations',


    'redis'      => [

        'cluster' => false,

        'default' => ['host'     => '127.0.0.1', 'port' => 6379,
                           'database' => 0],

    ],

];
