<?php


namespace App\Acme\Broker;


class Binance
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getSymbols()
    {
        return ['BTCUSDT','BNBUSDT'];

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

}
