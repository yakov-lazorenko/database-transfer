<?php

// этот скрипт читает статьи со старой базы (со старого сайта) 
// и записывает в новую БД

require_once 'bootstrap.php';

$reader = new ArticleReader;

$writer = new ArticleWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new ArticleAdvancedTransferManager($reader, $writer);

$manager->transfer();

