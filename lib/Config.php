<?php

class Config
{

    public static $data = [
        'echo_enabled' => false,
    ];




    public static function get($param)
    {

        if ( ! isset( self::$data[ $param ] ) ){

        	return null;

        }

        return self::$data[ $param ];

    }




    public static function set($param, $value)
    {
        self::$data[ $param ] = $value;
    }


}
