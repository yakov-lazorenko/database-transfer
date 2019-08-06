<?php

require_once 'bootstrap.php';

$reader = new SpecialThemesReader;

$writer = new SpecialThemesWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new AdvancedTransferManager($reader, $writer);
$manager->dataTitle = 'special themes';
$manager->dataBlockSize = 1000;

$manager->transfer();
