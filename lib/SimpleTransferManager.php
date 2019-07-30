<?php



class SimpleTransferManager implements TransferManager
{
    use ConsoleEcho;

    public $dataReader = null;
    public $dataWriter = null;

    public $dataTitle = 'objects';

    function __construct( $dataReader, $dataWriter )
    {

        $this->dataReader = $dataReader;
        $this->dataWriter = $dataWriter;

    }


    public function init(){}


    public function transfer()
    {

        $this->_echo(" >>> START : " . date("H:i:s", time()) );

		$this->_echo("reading {$this->dataTitle}");

        $this->dataReader->run();

        $this->dataWriter->setInputData( $this->dataReader->getData() );

		$this->_echo("writing {$this->dataTitle}");

        $this->dataWriter->run();

		$this->_echo(">>> SUCCESS : " . date("H:i:s", time()));

        $this->_e("\n");


    }


}


