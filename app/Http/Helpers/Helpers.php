<?php


/**
 * @param $string
 *
 * @return string
 */
function Pdo_Quote($string)
{
    return \DB::connection()->getPdo()->quote($string);
}

function log_info($message)
{
    if(is_array($message) || $message instanceof \Illuminate\Support\Collection){
        $message = print_r($message, true);
    }

    \Log::info($message);
}

function fix_persian_word($word)
{
    return str_replace('ي','ی',$word);
}
