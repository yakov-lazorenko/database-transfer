<?php


require_once 'bootstrap.php';

$reader = new ArchivePhotosReader;

$writer = new ArchivePhotosWriter;

$reader->init( $old_db_config );

$writer->init( $new_db_config );

$manager = new ArchivePhotosTransferManager($reader, $writer);

$manager->transfer();


