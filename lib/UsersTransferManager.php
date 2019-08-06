<?php


class UsersTransferManager extends AdvancedTransferManager
{
    public $dataBlockSize = 1000;


    public function transferCmsUsers()
    {
        $this->_echo(" >>> CMS users: START : " . date("H:i:s", time()));

        if (!$this->dataReader->connectToDb()) {
            $this->_echo("fail: cannot connect to the source DB" );
            return false;
        }

        if (!$this->dataWriter->connectToDb()) {
            $this->_echo("fail: cannot connect to the destination DB" );
            return false;
        }

        if (!$this->dataReader->readCmsUsers()) {
            $this->_echo("fail: readCmsUsers()" );
            return false;
        }

        $this->dataWriter->setInputData($this->dataReader->getData());

        if (!$this->dataWriter->writeCmsUsers()) {
            $this->_echo("fail: writeCmsUsers()" );
            return false;
        }

        if (!$this->dataWriter->writeCmsUsersTranslations()) {
            $this->_echo("fail: writeCmsUsersTranslations()" );
            return false;
        }

        $this->dataReader->disconnectFromDb();
        $this->dataWriter->disconnectFromDb();

        $this->_echo(" >>> CMS users: transferred success : END : " . date("H:i:s", time()));
        $this->_e("\n");

        return true;
    }



    public function truncateTables()
    {
        if (!$this->dataWriter->connectToDb()) {
            $this->_echo("fail: cannot connect to the destination DB" );
            return false;
        }

        if (!$this->dataWriter->truncateTables()) {
            $this->_echo("fail : truncateTables()");
            return false;
        }

        $this->dataWriter->disconnectFromDb();
        return true;
    }

}
