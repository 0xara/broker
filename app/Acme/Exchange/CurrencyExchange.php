<?php


namespace App\Acme\Exchange;


use Illuminate\Support\Facades\Http;

class CurrencyExchange implements Exchangable
{

    public static function getSymbols()
    {
        return CachedSymbols::of(CurrencyExchange::class)->remember(function () {
            return self::getSymbolsPrices();
        });
    }

    public static function getSymbolsPrices()
    {
        $response = Http::get("https://rahavard365.com/currency");

        preg_match_all('/,"id":"(\d+?)","type":"exchange.asset","info":{"trade_symbol":(.+?),"english_trade_symbol":(.+?),.+?"close_price":(.+?),/s',(string) $response,$result);

        $currencies = [];

        foreach ($result[2] as $key => $symbol) {
            $currencies[fix_persian_word(trim($symbol,'"'))] = [
                'symbol_code' => trim($result[1][$key],'"'),
                'symbol' => fix_persian_word(trim($symbol,'"')),
                'english_title' => trim($result[3][$key],'"'),
                'price' => $result[4][$key],
            ];
        }

        return $currencies;
    }

    public static function getSymbolPrice($symbol)
    {
        return self::getSymbols()[$symbol] ?? null;
    }
}
