<?php

namespace App\Console\Commands;

use App\Acme\CarbonFa\CarbonFa;
use App\Acme\Exchange\CachedSymbols;
use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\CurrencyExchange;
use App\Events\CurrencyExchangeSymbolsPricesUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BroadcastCurrencyExchangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:broadcast-currency-exchange-price {seconds=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'broadcast Currency Exchange price changes';

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
     */
    public function handle()
    {
        $seconds = $this->argument('seconds');

        $dt = Carbon::now();

        $x = 60 / $seconds;

        if($prices = $this->getPrices())
            CurrencyExchangeSymbolsPricesUpdated::dispatch($prices);

        $x--;

        /** time to close ended meetings before checking attendees */
        time_sleep_until($dt->addSeconds($seconds)->timestamp);

        do{
            if($prices = $this->getPrices())
                CurrencyExchangeSymbolsPricesUpdated::dispatch($prices);

            time_sleep_until($dt->addSeconds($seconds)->timestamp);

        } while(--$x > 0);
    }

    public function getPrices()
    {
        if(!$prices = CachedSymbols::of(CurrencyExchange::class)->get())
            return null;

        if(!count($prices = collect($prices))) return null;

        return $prices;
    }
}
