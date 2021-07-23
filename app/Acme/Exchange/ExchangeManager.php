<?php


namespace App\Acme\Exchange;


use Illuminate\Support\Str;

class ExchangeManager
{

    public static $drivers = [
        'Binance' => Binance::class,
        'TehranStockExchange' => TehranStockExchange::class,
        'CurrencyExchange' => CurrencyExchange::class,
    ];


    /**
     * @param $name
     * @return Exchangable|null
     */
    public static function getExchange($name)
    {
        $exchange = (string) Str::of($name)->title()->replace(' ','');

        if(!$exchange = self::$drivers[$exchange] ?: null) return null;

        return (new $exchange());
    }

}
