<?php

namespace App\Http\Controllers;

use App\Events\CurrencyExchangeSymbolsPricesUpdated;
use App\Events\TehranStockExchangeSymbolsPricesUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ApiUserBroadcastController extends Controller
{
    public function subscribed(Request $request)
    {
        switch ($request->input('channel')) {
            case TehranStockExchangeSymbolsPricesUpdated::$CHANNEL.".".TehranStockExchangeSymbolsPricesUpdated::$as:
                Artisan::call("exchange:watch-tehran-stock-exchange-price",[
                    'no-alert' => true, 'cached' => true
                ]);
                break;
            case CurrencyExchangeSymbolsPricesUpdated::$CHANNEL.".".TehranStockExchangeSymbolsPricesUpdated::$as:
                Artisan::call("exchange:watch-currency-exchange-price",[
                    'no-alert' => true, 'cached' => true
                ]);
                break;
        }

        return response()->json([]);
    }
}
