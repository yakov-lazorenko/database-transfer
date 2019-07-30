<?php


class ArchivePhotosTransferManager extends AdvancedTransferManager
{

	public $dataTitle = 'archive photos';

	public $dataBlockSize = 1000;

    public $commonColumns = '
		id
		title
		tags
		folder
    ';

    
    public $quotableColumns = '
		title
		tags
    ';


    public $timestampColumns = '';






    public function init()
    {

        $this->commonColumns = get_words_array_from_str($this->commonColumns);

        $this->quotableColumns = get_words_array_from_str($this->quotableColumns);

        $this->timestampColumns = get_words_array_from_str($this->timestampColumns);

        $this->dataReader->columns = $this->commonColumns;

        $this->dataWriter->entityTableAllColumns = $this->commonColumns;

        $this->dataWriter->quotableColumnsInEntityTable = $this->quotableColumns;

        $this->dataWriter->timestampColumnsInEntityTable = $this->timestampColumns;

    }


}
