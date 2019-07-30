<?php



class SpecialThemesReader extends AdvancedReader
{

    // settings for old DB :

    public $entityTableName = 'special';

    public $entityTranslationTableName = 'special_translate';

    public $translatableColumns = [
        'title', 'alias',
        'meta_title', 'meta_keywords', 'meta_description',
        'active'
    ];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'special_id';


 



    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {

        $entity['id'] = $data['id'];

        $entity['position'] = 0;

        $entity['sidebar'] = 0;

        $entity['translations'] =
            $this->prepareEntityTranslations($data, $this->translatableColumns);

        return $entity;

    }





}
