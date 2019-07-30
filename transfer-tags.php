<?php

// этот скрипт читает тэги со старой базы (со старого сайта) 
// и записывает в новую БД

require_once 'bootstrap.php';

$reader = new TagReader;

$writer = new TagWriter;

$reader->init( $old_db_config );

$writer->init( $new_db_config );

$manager = new SimpleTransferManager($reader, $writer);
$manager->dataTitle = 'tags';

$manager->transfer();
