<?php

// этот скрипт читает категории со старой базы (со старого сайта) 
// и записывает в новую БД

require_once 'bootstrap.php';

$reader = new CategoryReader;

$writer = new CategoryWriter;

$reader->init( $old_db_config );

$writer->init( $new_db_config );

$manager = new SimpleTransferManager($reader, $writer);
$manager->dataTitle = 'categories';

$manager->transfer();
