<?php

return ['fetch'       => PDO::FETCH_CLASS, 'default' => 'mysql',
        'connections' => ['mysql' => ['driver'    => 'mysql',
                                      'host'      => 'localhost',
                                      'database'  => 'FireflyTest',
                                      'username'  => 'Firefly',
                                      'password'  => 'Firefly',
                                      'charset'   => 'utf8',
                                      'collation' => 'utf8_unicode_ci',
                                      'prefix'    => '',],],];
