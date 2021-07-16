<?php


namespace App\Acme\Exchange;


use Illuminate\Support\Str;

class Binance implements Exchangable
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getSymbols()
    {
        $http = \Http::get('https://api.binance.com/api/v3/exchangeInfo');
        $response = $http->json();

        return $response['symbols'];
    }

    public static function getSymbolNames()
    {
        return collect(self::getSymbols()['symbols'])->map(function ($item, $key) {
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

    public static function getSymbolsPricesDummy()
    {
        return [["symbol" => "ETHBTC","price" => "0.06849800" ], [ "symbol" => "LTCBTC","price" => "0.00474300" ] ];
    }

}
