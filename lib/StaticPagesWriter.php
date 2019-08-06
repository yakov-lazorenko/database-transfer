<?php


class StaticPagesWriter extends AdvancedWriter
{
    // settings for NEW DB :

    public $entityTableName = 'pages';

    public $entityTranslationTableName = 'pages_translations';

    public $entityTableAllColumns = [ 'id', 'alias', 'position' ];

    // in translation table
    public $translatableColumns = [
        'title', 'text_full',
        'meta_title', 'meta_keywords', 'meta_description',
    ];

    // столбцы, значения которых нужно взять в кавычки
    public $quotableColumnsInEntityTable = [ 'alias' ];

    public $quotableColumnsInTranslationTable = [
        'title', 'text_full',
        'meta_title', 'meta_keywords', 'meta_description'
    ];

    // entity id column name in translation table in NEW DB
    public $entityIdColumnName = 'page_id';

}
