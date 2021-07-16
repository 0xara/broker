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
