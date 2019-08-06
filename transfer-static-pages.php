<?php

require_once 'bootstrap.php';

$reader = new StaticPagesReader;

$writer = new StaticPagesWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new AdvancedTransferManager($reader, $writer);
$manager->dataTitle = 'static pages';
$manager->dataBlockSize = 1000;

$manager->transfer();
