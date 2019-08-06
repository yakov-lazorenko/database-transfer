<?php

require_once 'bootstrap.php';

$reader = new UsersReader;

$writer = new UsersWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new UsersTransferManager($reader, $writer);

$manager->truncateTables();

$manager->transferCmsUsers();

$manager->transferSiteUsers();
