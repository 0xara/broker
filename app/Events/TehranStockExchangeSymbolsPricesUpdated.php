<?php

namespace App\Events;

use App\Models\TehranStockExchangeShare;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\Fractalistic\ArraySerializer;

class TehranStockExchangeSymbolsPricesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public static $CHANNEL = "tehran_stock_exchange";
    public static $as = "symbols_prices_updated";

    /**
     * @var array
     */
    public $priceList;

    public $groups = [];

    /**
     * Create a new event instance.
     *
     * @param Collection $prices
     */
    public function __construct($prices = [])
    {
        $prices = collect($prices->all())->forget('index');

        $this->groups = TehranStockExchangeShare::groupBy('group_code','group_name')->pluck('group_name','group_code');

        $this->priceList = fractal()->collection($prices)->transformWith(function ($symbol) {
            return [
                TehranStockExchangeShare::stock_code => $symbol[TehranStockExchangeShare::stock_code],
                // TehranStockExchangeShare::instrument_id => $symbol[TehranStockExchangeShare::instrument_id],
                TehranStockExchangeShare::update_at=> $symbol[TehranStockExchangeShare::update_at] * 1000,
                TehranStockExchangeShare::symbol => $symbol[TehranStockExchangeShare::symbol],
                TehranStockExchangeShare::price => $symbol[TehranStockExchangeShare::price],
                TehranStockExchangeShare::first_price => $symbol[TehranStockExchangeShare::first_price],
                TehranStockExchangeShare::min_price => $symbol[TehranStockExchangeShare::min_price],
                TehranStockExchangeShare::max_price => $symbol[TehranStockExchangeShare::max_price],
                TehranStockExchangeShare::transactions_volume => $symbol[TehranStockExchangeShare::transactions_volume],
                TehranStockExchangeShare::group_code => $symbol[TehranStockExchangeShare::group_code],
            ];
        })
        ->serializeWith(ArraySerializer::class)
        ->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(self::$CHANNEL);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return self::$as;
    }
}
