<?php


class SpecialThemesWriter extends AdvancedWriter
{
    // settings for NEW DB :

    public $entityTableName = 'special';
    public $entityTranslationTableName = 'special_translations';
    public $entityTableAllColumns = [ 'id', 'position', 'sidebar' ];

    // in translation table
    public $translatableColumns = [
        'title', 'alias',
        'meta_title', 'meta_keywords', 'meta_description',
        'active'
    ];

    public $quotableColumnsInTranslationTable = [
        'title', 'alias',
        'meta_title', 'meta_keywords', 'meta_description'
    ];

    // entity id column name in translation table in NEW DB
    public $entityIdColumnName = 'special_id';

}
