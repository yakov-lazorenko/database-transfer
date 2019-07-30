<?php


require_once 'bootstrap.php';

$reader = new PhotoArticleReader;

$writer = new PhotoArticleWriter;

$reader->init( $old_db_config );

$writer->init( $new_db_config );

$manager = new AdvancedTransferManager($reader, $writer);
$manager->dataTitle = 'photo-article';

$manager->transfer();
