<?php

return new \Phalcon\Config(array(
	'database' => array(
		'host'     => 'localhost',
		'username' => 'root',
		'password' => '',
		'dbname'   => 'db_lirikita',
	),
	'dir' => array(
		'controllersDir' => '../app/controllers/',
		'modelsDir'      => '../app/models/',
		'viewsDir'       => '../app/views/',
		'baseDir'        => '../app/base/',
		'configDir'      => '../app/config/',
		'pluginsDir'     => '../app/plugins/',
		'libraryDir'     => '../app/library/',
	),
	'file' => array(
		'log_db' => '../app/logs/db.log',
	),
));
