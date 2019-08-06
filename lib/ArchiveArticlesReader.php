<?php


class ArchiveArticlesReader extends AdvancedReader
{
    // settings for old DB :
    public $withTranslations = false;
    public $entityTableName = 'articles';


    public function prepareEntityData($data)
    {
        $data['title'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);        

        $data['text_short'] = htmlspecialchars_decode($data['text_short'], ENT_QUOTES);
        
        $data['text_full'] = htmlspecialchars_decode($data['text_full'], ENT_QUOTES);

        $data['text_full_clean'] = '';

        $data['text_short_main'] = '';

        $data['meta_description'] = htmlspecialchars_decode($data['meta_description'], ENT_QUOTES);

        return $data;
    }

}
