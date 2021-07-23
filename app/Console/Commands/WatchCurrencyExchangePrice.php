<?php

namespace App\Console\Commands;

use App\Acme\CarbonFa\CarbonFa;
use App\Acme\Exchange\CachedSymbols;
use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\CurrencyExchange;
use App\Events\CurrencyExchangeSymbolsPricesUpdated;
use Illuminate\Console\Command;

class WatchCurrencyExchangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:watch-currency-exchange-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'watch Currency Exchange price changes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prices = collect(self::getPrices());

        CurrencyExchangeSymbolsPricesUpdated::dispatch($prices);

        if(self::marketIsOpen()) {
            SendAlertNotification::handle($prices);
        }
    }

    public static function getPrices()
    {
        if(self::marketIsOpen())
        {
            return CurrencyExchange::getSymbolsPrices();
        }

        return CachedSymbols::of(CurrencyExchange::class)->remember(function (){
            return CurrencyExchange::getSymbolsPrices();
        });
    }

    public static function marketIsOpen()
    {
        $today = CarbonFa::now();

        return !$today->isFriday();
    }
}
