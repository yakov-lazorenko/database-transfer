<?php


class StaticPagesReader extends AdvancedReader
{
    // settings for old DB :

    public $entityTableName = 'page';
    public $entityTranslationTableName = 'page_translate';
    public $translatableColumns = [
        'title', 'alias', 'text_full',
        'meta_title', 'meta_keywords', 'meta_description',
    ];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'page_id';



    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {
        $entity['id'] = $data['id'];
        $entity['position'] = 0;
        $entity['alias'] = $data['alias'];
        $data['text_full'] = htmlspecialchars_decode($data['text_full'], ENT_QUOTES);
        $data[ $this->translation_prefix . 'text_full' ] = htmlspecialchars_decode(
            $data[ $this->translation_prefix . 'text_full' ], ENT_QUOTES);
        $entity['translations'] =
            $this->prepareEntityTranslations($data, $this->translatableColumns);
        return $entity;
    }

}
