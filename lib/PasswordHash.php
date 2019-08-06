<?php


class PasswordHash
{

	public static function makeHash($value)
    {
        return password_hash($value, PASSWORD_BCRYPT, ['cost' => 10]);
    }



    public static function checkHash($value, $hashedValue)
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

}
