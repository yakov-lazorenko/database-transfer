<?php


class ContestsWriter extends AdvancedWriter
{
    // settings for NEW DB :

    public $entityTableName = 'contests';
    public $entityTranslationTableName = 'contests_translations';
    public $entityTableAllColumns = [ 'id', 'date_start', 'date_end' ];

    // in translation table
    public $translatableColumns = [
        'title', 'link', 'counter', 'photo_title', 'active',
    ];

    public $quotableColumnsInTranslationTable = [
        'title', 'link', 'counter', 'photo_title',
    ];

    public $timestampColumnsInEntityTable = [ 'date_start', 'date_end' ];

    // entity id column name in translation table in NEW DB
    public $entityIdColumnName = 'contest_id';

}
