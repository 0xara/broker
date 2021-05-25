<?php

namespace App\Console\Commands;

use App\Acme\Broker\Binance;
use App\Models\Alert;
use App\Models\User;
use App\Notifications\AlertActivated;
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
    protected $signature = 'broker:watch-binance-price {seconds=2}';

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

        $this->sendAlertNotification();
        $x--;

        /** time to close ended meetings before checking attendees */
        time_sleep_until($dt->addSeconds($seconds)->timestamp);

        do{

            $this->sendAlertNotification();

            time_sleep_until($dt->addSeconds($seconds)->timestamp);

        } while(--$x > 0);
    }

    /**
     *
     */
    public function sendAlertNotification()
    {
        \Log::info('schedule ping');
        $symbolObjects = Binance::getSymbolsPrices();

        $existSymbolsInDB = Alert::groupBy('symbol')->pluck('symbol');

        /** @var Builder $alerts */
        $alerts = Alert::query();
        $updateQuery =
            "UPDATE ".($alertTable = Alert::newModelInstance()->getTable()).
            " SET current_position= (" .
            "CASE WHEN current_position='".Alert::DOWN_POSITION."'".
            " THEN '".Alert::UP_POSITION."'".
            " ELSE '".Alert::DOWN_POSITION."'".
            " END) WHERE {$alertTable}.repeat = 1 AND (";

        $is_first = true;

        foreach ($symbolObjects as $KEY => $symbolObj)
        {
            if(! $existSymbolsInDB->contains($symbolObj['symbol'])) continue;

            $alerts->orWhere(function ($q) use ($KEY, $symbolObj) {
                /** @var Builder $q */
                $q->where('symbol','=',$symbolObj['symbol']);
                $q->where('active','=',1);
                $q->whereRaw(
                    "CASE " .
                    " WHEN Operator='".Alert::GTE."' AND current_position='".Alert::DOWN_POSITION."' THEN price"." <= ".$symbolObj['price'].
                    " WHEN Operator='".Alert::LTE."' AND current_position='".Alert::UP_POSITION."' THEN price"." >= ".$symbolObj['price'].
                    //" WHEN Operator='".Alert::CROSS."' AND current_position='".Alert::UP_POSITION."' THEN price"." < ".$symbolObj['price'].
                    //" WHEN Operator='".Alert::CROSS."' AND current_position='".Alert::DOWN_POSITION."' THEN price"." > ".$symbolObj['price'].
                    " ELSE 0 END"
                );
            });

            $updateQuery .= !$is_first ? ' OR ' : '';
            $is_first = false;

            $updateQuery .= "(" .
                "symbol='{$symbolObj['symbol']}' AND ".
                "( CASE " .
                " WHEN Operator='".Alert::GTE."' AND current_position='".Alert::UP_POSITION."' THEN price"." > ".$symbolObj['price'].
                " WHEN Operator='".Alert::LTE."' AND current_position='".Alert::DOWN_POSITION."' THEN price"." < ".$symbolObj['price'].
               // " WHEN Operator='".Alert::CROSS."' AND current_position='".Alert::DOWN_POSITION."' THEN price"." > ".$symbolObj['price'].
                //" WHEN Operator='".Alert::CROSS."' AND current_position='".Alert::UP_POSITION."' THEN price"." < ".$symbolObj['price'].
                " ELSE 0 END )" .
                ")";
        }

        $alerts = $alerts->with('user')->get();

        // update alerts that triggered before
        $updateQuery .= (!count($alerts) ? " ) " : ") AND {$alertTable}.id NOT in (".implode(',',$alerts->modelKeys()).")");
        \DB::statement($updateQuery);

        if(count($alerts)) {
            \DB::statement(
                "UPDATE {$alertTable} ".
                " SET current_position= (".
                "CASE WHEN current_position='".Alert::DOWN_POSITION."'".
                " THEN '".Alert::UP_POSITION."'".
                " ELSE '".Alert::DOWN_POSITION."'".
                "END) " .
                "WHERE {$alertTable}.id in (".implode(',',$alerts->modelKeys()).")"
            );
        }

        foreach ($alerts as $alert)
        {
            /** @var User $user */
            $user = $alert->user;
            $user->notify(new AlertActivated($alert));
        }
    }

}
