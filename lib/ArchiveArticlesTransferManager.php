<?php


class ArchiveArticlesTransferManager extends AdvancedTransferManager
{
	public $dataTitle = 'archive articles';

	public $dataBlockSize = 1000;

    public $commonColumns = '
		id
		pictures_id
		gallery_id
		polls_id
		active
		title
		text_short
		text_full
		meta_title
		meta_keywords
		meta_description
		title_ukrnet
		tags
		date_pub
		date_stop
		date_created
		date_changed
		counter_comments
		counter_views
		connected_articles
		slider_main
		slider_attached
		is_adv
		yandex_rss
		mailru_informer
		rss_state
    ';
    
    public $quotableColumns = '
		title
		text_short
		text_full
		meta_title
		meta_keywords
		meta_description
		title_ukrnet
		tags
		connected_articles
		text_full_clean
		text_short_main
    ';


    public $timestampColumns = '';



    public function init()
    {
        $this->commonColumns = get_words_array_from_str($this->commonColumns);

        $this->quotableColumns = get_words_array_from_str($this->quotableColumns);

        $this->timestampColumns = get_words_array_from_str($this->timestampColumns);

        $this->dataReader->columns = $this->commonColumns;

        $this->dataWriter->entityTableAllColumns = array_merge(
        	$this->commonColumns, ['text_full_clean', 'text_short_main'] );

        $this->dataWriter->quotableColumnsInEntityTable = $this->quotableColumns;

        $this->dataWriter->timestampColumnsInEntityTable = $this->timestampColumns;
    }

}
