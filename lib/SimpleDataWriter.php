<?php


abstract class SimpleDataWriter implements DataWriter
{
    use ConsoleEcho;

    public $db_connection = null;
    public $settings = null;
    public $inputData = null;

    abstract public function writeData();
    abstract public function truncateTables();


    public function init($settings)
    {
        $this->settings = $settings;
    }



    public function run()
    {
        $this->db_connection = new DatabaseConnection;

        if (!$this->db_connection->connect( $this->settings )) {

            $this->_echo($this->db_connection->error);
            return false;
        }

        if (!$this->truncateTables()) {
            $this->_echo("fail : truncateTables()");
            return false;
        }

        if (!$this->writeData()) {
            $this->_echo("fail : writeCategories()");
            return false;
        }

        $this->db_connection->disconnect();

        return true;
    }



    public function setInputData($data)
    {
        $this->inputData = $data;
    }


}
