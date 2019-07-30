<?php


require_once 'bootstrap.php';

$reader = new ArchiveArticlesReader;

$writer = new ArchiveArticlesWriter;

$reader->init( $old_db_config );

$writer->init( $new_db_config );

$manager = new ArchiveArticlesTransferManager($reader, $writer);

$manager->transfer();


