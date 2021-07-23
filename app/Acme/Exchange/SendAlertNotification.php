<?php


namespace App\Acme\Exchange;


use App\Events\AlertActivated;
use App\Models\Alert;
use App\Models\User;
use App\Notifications\AlertActivatedNotification;
use Illuminate\Database\Eloquent\Builder;

class SendAlertNotification
{

    public static function handle($symbolObjects)
    {
        \Log::info('schedule ping');

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

        $events = [];

        foreach ($alerts as $alert)
        {
            /** @var User $user */
            $user = $alert->user;
            $user->notify(new AlertActivatedNotification($alert));
            if(!($events[$alert->user_id] ?? '')) $events[$alert->user_id] = [];
            $events[$alert->user_id][] = [
                'id' => $alert->id,
                'symbol' => $alert->symbol,
                'operator' => $alert->operator,
                'price' => $alert->price,
                'message' => with($alert, function ($alert) {
                    return $alert->symbol.AlertActivatedNotification::OPERATORS[$alert->operator].((float) $alert->price);
                })
            ];
        }

        foreach ($events as $user_id => $event) {
            AlertActivated::dispatch($user_id,$alerts);
        }
    }

}
