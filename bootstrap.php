<?php

require_once 'lib/functions.php';

spl_autoload_register(function ($class_name) {
    require_once 'lib/' . $class_name . '.php';
});

Config::set('echo_enabled', true);

$old_db_config = [
	'database' => 'newtvoymalyshcomua',
	'host' => 'localhost',
	'user' => 'root',
	'password' => '1'
];

$new_db_config = [
	'database' => 'tvoymalysh_test',
	'host' => 'localhost',
	'user' => 'root',
	'password' => '1' 
];
