<?php

// этот скрипт переносит строки таблицы `tag_article` 
// со старой БД в новую (`article_tag`)

require_once 'bootstrap.php';

$reader = new ArticleTagReader;

$writer = new ArticleTagWriter;

$reader->init($old_db_config);

$writer->init($new_db_config);

$manager = new SimpleTransferManager($reader, $writer);
$manager->dataTitle = 'article-tag';

$manager->transfer();
