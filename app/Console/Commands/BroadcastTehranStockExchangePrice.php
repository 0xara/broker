<?php

namespace App\Console\Commands;

use App\Acme\CarbonFa\CarbonFa;
use App\Acme\Exchange\CachedSymbols;
use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\TehranStockExchange;
use App\Events\TehranStockExchangeSymbolsPricesUpdated;
use App\Models\TehranStockExchangeShare;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BroadcastTehranStockExchangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:broadcast-tehran-stock-exchange-price {seconds=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'broadcast Tehran Exchange price changes';

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
            TehranStockExchangeSymbolsPricesUpdated::dispatch($prices);

        $x--;

        /** time to close ended meetings before checking attendees */
        time_sleep_until($dt->addSeconds($seconds)->timestamp);

        do{

            if($prices = $this->getPrices())
                TehranStockExchangeSymbolsPricesUpdated::dispatch($prices);

            time_sleep_until($dt->addSeconds($seconds)->timestamp);

        } while(--$x > 0);
    }

    public function getPrices()
    {
        if(!$prices = CachedSymbols::of(TehranStockExchange::class)->get())
            return null;

        if(!count($prices = collect($prices))) return null;

        if($prices->has('index')) {
            $indexData = $prices->get('index');
            $indexData[TehranStockExchangeShare::price] =  $indexData[TehranStockExchangeShare::value];
            $prices->put('index',$indexData);
        }

        return $prices;
    }
}
