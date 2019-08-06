<?php


class ContestsReader extends AdvancedReader
{
    // settings for old DB :

    public $entityTableName = 'entry_points';
    public $entityTranslationTableName = 'entry_points_translate';
    public $translatableColumns = [
        'url', 'counter', 'text_short', 'active',
    ];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'entry_point_id';


    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {
        $entity['id'] = $data['id'];

        $entity['date_start'] = mktime(0, 0, 0, 1, 1, 2017);
        $entity['date_end'] = mktime(0, 0, 0, 1, 1, 2018);

        $translatableColumns = array_merge($this->translatableColumns, ['photo_title']);

        $renamedColumns = [ 'url' => 'link', 'text_short' => 'title' ];

        $data['text_short'] = htmlspecialchars_decode($data['text_short'], ENT_QUOTES);

        $data[ $this->translation_prefix . 'text_short' ] = 
            htmlspecialchars_decode($data[ $this->translation_prefix . 'text_short' ], ENT_QUOTES);

        $data['photo_title'] = $data['text_short'];

        $data[ $this->translation_prefix . 'photo_title'] =
            $data[ $this->translation_prefix . 'text_short' ];

        $entity['translations'] =
            $this->prepareEntityTranslations($data, $translatableColumns, $renamedColumns);

        return $entity;
    }

}
