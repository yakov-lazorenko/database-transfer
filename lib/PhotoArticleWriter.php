<?php



class PhotoArticleWriter extends AdvancedWriter
{

    // settings for NEW DB :

    public $entityTableName = 'photos_articles';

    public $entityTranslationTableName = 'photos_articles_translations';



    public $entityTableAllColumns = [ 'id', 'url' ];

    // in translation table
    public $translatableColumns = ['title', 'source'];

    // столбцы, значения которых нужно взять в кавычки
    public $quotableColumnsInEntityTable = ['url'];

    public $quotableColumnsInTranslationTable = [ 'title', 'source'];



    // entity id column name in translation table in NEW DB
    public $entityIdColumnName = 'photo_article_id';



}


