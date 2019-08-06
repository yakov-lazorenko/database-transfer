<?php


class GalleryReader extends AdvancedReader
{
    // settings for old DB :

    public $entityTableName = 'gallery';
    public $entityTranslationTableName = 'gallery_translate';

    public $translatableColumns = [
        'title', 'description',
        'meta_title', 'meta_keywords', 'meta_description',
        'date_pub', 'active', 'views'
    ];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'gallery_id';

    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {
        $translatableColumns = array_merge( $this->translatableColumns, ['created_at'] );
        $renamedColumns = [ 'description' => 'text_full' ];
        $entity['id'] = $data['id'];
        $data['created_at'] = time();
        $data[ $this->translation_prefix . 'created_at'] = $data['created_at'];

        $entity['translations'] =
            $this->prepareEntityTranslations($data, $translatableColumns, $renamedColumns);

        return $entity;
    }

}
