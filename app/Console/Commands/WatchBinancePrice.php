<?php

namespace App\Console\Commands;

use App\Acme\Exchange\Binance;
use App\Acme\Exchange\SendAlertNotification;
use App\Models\Alert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class WatchBinancePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:watch-binance-price {seconds=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'watch Binance price changes';

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
        $seconds = $this->argument('seconds');

        $dt = Carbon::now();

        $x = 60 / $seconds;

        SendAlertNotification::handle(Binance::getSymbolsPrices());
        $x--;

        /** time to close ended meetings before checking attendees */
        time_sleep_until($dt->addSeconds($seconds)->timestamp);

        do{

            SendAlertNotification::handle(Binance::getSymbolsPrices());

            time_sleep_until($dt->addSeconds($seconds)->timestamp);

        } while(--$x > 0);
    }

}
