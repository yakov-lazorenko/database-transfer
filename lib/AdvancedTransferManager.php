<?php


class AdvancedTransferManager implements TransferManager
{
    use ConsoleEcho;

    public $dataReader = null;
    public $dataWriter = null;

    public $dataTitle = 'objects';

    public $dataSize = 0;
    public $dataBlockSize = 1000;


    function __construct($dataReader, $dataWriter)
    {
        $this->dataReader = $dataReader;
        $this->dataWriter = $dataWriter;

        $dataReader->transferManager = $this;
        $dataWriter->transferManager = $this;

        $this->init();
    }


    public function init()
    {
        //
    }


    public function beforeTransfer()
    {
        //
    }


    public function transfer()
    {
        $this->_echo(" >>> '$this->dataTitle' >>> START: " . date("H:i:s", time()));

        if (!$this->dataReader->connectToDb()) {
            $this->_echo("fail: cannot connect to the source DB" );
            return false;
        }

        if (!$this->dataWriter->connectToDb()) {
            $this->_echo("fail: cannot connect to the destination DB" );
            return false;
        }

        $this->beforeTransfer();

        if (!$this->dataWriter->truncateTables()) {
            $this->_echo( "fail : truncateTables()" );
            return false;
        }

        $size = $this->getDataSize();

        $n_data_blocks = $size % $this->dataBlockSize ? 
            (int)( $size / $this->dataBlockSize ) + 1 : 
            (int)( $size / $this->dataBlockSize );

        for ($n = 0; $n < $n_data_blocks; $n ++) {
            if ($n == $n_data_blocks - 1) {
                $current_block_size = $size % $this->dataBlockSize ? : $this->dataBlockSize;
            } else {
                $current_block_size = $this->dataBlockSize;
            }

            $offset = $n * $this->dataBlockSize;
            $limit = $current_block_size;
            $N = $n + 1;

            $this->_echo( "reading {$this->dataTitle}, data block $N, " . date("H:i:s", time()) );

            $this->dataReader->initDataBlock($offset, $limit);
            $this->dataReader->run();

            $this->_echo("writing {$this->dataTitle}, data block $N, " . date("H:i:s", time()) );

            $this->dataWriter->setInputData($this->dataReader->getData());
            $this->dataWriter->run();
        }

        $this->dataReader->disconnectFromDb();
        $this->dataWriter->disconnectFromDb();

        $this->_echo(" >>> SUCCESS: $size '{$this->dataTitle}' transferred, " . date("H:i:s", time()));
        $this->_e("\n");

        return true;
    }



    public function getDataSize()
    {
        if (!$this->dataSize) {
            $this->dataSize = $this->dataReader->getDataSize();
        }

        return $this->dataSize;
    }

}
