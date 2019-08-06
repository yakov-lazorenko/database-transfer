<?php


class GalleryWriter extends AdvancedWriter
{
    // settings for NEW DB :

    public $entityTableName = 'galleries';
    public $entityTranslationTableName = 'galleries_translations';
    public $entityTableAllColumns = [ 'id' ];

    // in translation table
    public $translatableColumns = [
        'title', 'text_full',
        'meta_title', 'meta_keywords', 'meta_description',
        'date_pub', 'active', 'views', 'created_at'
    ];

    // столбцы, значения которых нужно взять в кавычки
    public $quotableColumnsInEntityTable = [];

    public $quotableColumnsInTranslationTable = [
        'title', 'text_full',
        'meta_title', 'meta_keywords', 'meta_description'
    ];

    public $timestampColumnsInTranslationTable = [ 'date_pub', 'created_at' ];

    // entity id column name in translation table in NEW DB
    public $entityIdColumnName = 'gallery_id';

}
