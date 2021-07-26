<?php

namespace App\Console\Commands;

use App\Acme\CarbonFa\CarbonFa;
use App\Acme\Exchange\CachedSymbols;
use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\TehranStockExchange;
use App\Events\TehranStockExchangeSymbolsPricesUpdated;
use Illuminate\Console\Command;

class WatchTehranStockExchangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:watch-tehran-stock-exchange-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'watch Tehran Exchange price changes';

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

        if($prices->has('index')) {
            $indexData = $prices->get('index');
            $indexData['price'] =  $indexData['value'];
            $prices->put('index',$indexData);
        }

        TehranStockExchangeSymbolsPricesUpdated::dispatch($prices);

        if(self::marketIsOpen()) {
            SendAlertNotification::handle($prices);
        }
    }

    public static function getPrices()
    {
        if(self::marketIsOpen())
        {
            return CachedSymbols::of(TehranStockExchange::class)->save(function (){
                return TehranStockExchange::getSymbolsPrices();
            });
        }

        return CachedSymbols::of(TehranStockExchange::class)->remember(function (){
            return TehranStockExchange::getSymbolsPrices();
        });
    }

    public static function marketIsOpen()
    {
        $today = CarbonFa::now(new \DateTimeZone('Asia/Tehran'));

        return !$today->isThursday() && !$today->isFriday() &&
            $today->getHour() > 8 && $today->getHour() < 13;
    }
}
