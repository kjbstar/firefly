<?php

return array(


	'fetch' => PDO::FETCH_CLASS,

	'default' => 'mysql',


	'migrations' => 'migrations',



	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
