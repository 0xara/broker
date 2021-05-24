<?php


namespace App\Acme\Broker;


use Illuminate\Support\Str;

class Binance
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getSymbols()
    {
        $http = \Http::get('https://api.binance.com/api/v3/exchangeInfo');
        $response = $http->json();

        return collect($response['symbols'])->map(function ($item, $key) {
            return $item['symbol'];
        });
    }

    public static function getSymbolsPrices()
    {
        $http = \Http::get('https://api.binance.com/api/v3/ticker/price');
        $response = $http->json();

        return collect($response);
    }

    public static function getSymbolPrice($symbol)
    {
        $http = \Http::get('https://api.binance.com/api/v3/ticker/price?symbol='.Str::upper($symbol));
        $response = $http->json();

        return $response['price'] ?? null;
    }

}
