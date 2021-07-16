<?php


namespace App\Acme\Exchange;


interface Exchangable
{

    public static function getSymbols();

    public static function getSymbolPrice($symbol);

}
