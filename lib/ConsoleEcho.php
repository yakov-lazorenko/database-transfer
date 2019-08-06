<?php


trait ConsoleEcho
{
	public $beginEchoString = "\n --- ";
	public $endEchoString = " ---\n";

	public function _echo($message)
	{
	    if (Config::get('echo_enabled')) {
	        echo $this->beginEchoString . $message . $this->endEchoString;
	    }
	}



	public function _e($message)
	{
	    if (Config::get('echo_enabled')) {
	        echo $message;
	    }
	}



	public function _ee($message, $begin = '', $end = '')
	{
	    if (Config::get('echo_enabled')) {
	        echo $begin . $message . $end;
	    }
	}



	public function dd($var)
	{
        print_r($var);
        exit;
	}

}
