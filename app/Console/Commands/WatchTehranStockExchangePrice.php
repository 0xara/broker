<?php

namespace App\Console\Commands;

use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\TehranStockExchange;
use App\Events\TehranStockExchangeSymbolsPricesUpdated;
use App\Models\Alert;
use App\Models\User;
use App\Notifications\AlertActivated;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class WatchTehranStockExchangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:watch-tehran-exchange-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'watch Tehran Exchange price changes';

    /**
     * @var Alert[]|\Illuminate\Database\Eloquent\Collection
     */
    private $alerts;

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

        TehranStockExchangeSymbolsPricesUpdated::dispatch($prices = TehranStockExchange::getSymbolsPrices());

        SendAlertNotification::handle($prices);
    }
}
