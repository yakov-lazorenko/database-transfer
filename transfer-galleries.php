<?php

require_once 'bootstrap.php';

$reader = new GalleryReader;

$writer = new GalleryWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new AdvancedTransferManager($reader, $writer);
$manager->dataTitle = 'galleries';

$manager->transfer();
