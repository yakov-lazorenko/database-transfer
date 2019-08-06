<?php


class PhotoArticleReader extends AdvancedReader
{
    // settings for old DB :
    public $entityTableName = 'avatar';
    public $entityTranslationTableName = 'avatar_translate';
    public $translatableColumns = ['title', 'source', 'url'];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'avatar_id';

    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {
        // in NEW DB
        $translatableColumns = ['source', 'title'];
        $entity['id'] = $data['id'];
        $entity['url'] = $data['url'];
        $entity['translations'] = $this->prepareEntityTranslations($data, $translatableColumns);

        return $entity;
    }

}
