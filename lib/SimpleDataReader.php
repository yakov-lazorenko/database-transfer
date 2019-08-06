<?php


abstract class SimpleDataReader implements DataReader
{
    use ConsoleEcho;

	const LANG_RU = 1;
	const LANG_UK = 2;
    
    public $localeCode = [ self::LANG_RU => 'ru', self::LANG_UK => 'uk' ];
    public $db_connection = null;
    public $settings = null;


    abstract public function readData();

    abstract public function getData();



    public function init($settings)
    {
        $this->settings = $settings;
    }



    public function run()
    {
        $this->db_connection = new DatabaseConnection;

        if (!$this->db_connection->connect( $this->settings )){
        	$this->_echo( $this->db_connection->error );
        	return false;
        }

        if (!$this->readData()) {
            $this->_echo( "fail : readData()" );
            return false;
        }
		
        $this->db_connection->disconnect();

        return true;
    }

}
