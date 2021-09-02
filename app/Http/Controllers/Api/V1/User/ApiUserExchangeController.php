<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Acme\Exchange\ExchangeManager;
use App\Http\Controllers\Controller;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;

class ApiUserExchangeController extends Controller
{
    public function index()
    {
        $exchanges = Exchange::select(['name','id'])->get();

        return fractal()->collection($exchanges)->transformWith(function ($exchange) {
            return [
                'exchange_id' => $exchange->id,
                'name' => $exchange->name,
            ];
        })
            ->serializeWith(ArraySerializer::class)
            ->toArray();
    }

    public function symbolsSelectBoxList(Request $request)
    {
        $exchange = Exchange::findOrFail($request->input('exchange'));

        $symbols = ExchangeManager::getExchange($exchange->name)->getSymbols();

        if(!count($symbols)) return $symbols;

        if(!($symbols[0]['quoteAsset'] ?? '')) {

            return collect($symbols)->pluck('symbol')->toArray();
        }

        $result = [];

        foreach ($symbols as $symbol) {
            if(!array_key_exists($symbol['quoteAsset'],$result)) {
                $result[$symbol['quoteAsset']] = [];
            }
            $result[$symbol['quoteAsset']][] = $symbol['symbol'];
        }

        return $result;
    }
}
