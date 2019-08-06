<?php

require_once 'lib/functions.php';

spl_autoload_register(function ($class_name) {
    require_once 'lib/' . $class_name . '.php';
});

Config::set('echo_enabled', true);

$old_db_config = [
    'database' => 'old_database',
    'host' => 'localhost',
    'user' => 'root',
    'password' => '123'
];

$new_db_config = [
    'database' => 'new_database',
    'host' => 'localhost',
    'user' => 'root',
    'password' => '123' 
];
