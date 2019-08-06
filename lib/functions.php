<?php


function dd($var)
{
    print_r($var);
    exit;
}



function d($var)
{
    print_r($var);
}



function darr($arr, $offset, $limit)
{
    print_r(array_slice($arr, $offset, $limit));
    exit;
}



function darrc($arr)
{
    print_r(count($arr));
    exit;
}



function dmem()
{
    print_r(memory_get_usage());
    exit;
}



function get_words_array_from_str($str)
{
    $str = preg_replace("/[[:space:]]+/", ' ', $str);

    return explode(' ', trim($str) );
}
