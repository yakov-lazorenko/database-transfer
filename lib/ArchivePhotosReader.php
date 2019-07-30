<?php



class ArchivePhotosReader extends AdvancedReader
{

    // settings for old DB :

    public $withTranslations = false;

    public $entityTableName = 'pictures';






    public function prepareEntityData($data)
    {

        return $data;

    }





}
