<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\Fractalistic\ArraySerializer;

class CurrencyExchangeSymbolsPricesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public static $CHANNEL = "currency_exchange";
    public static $as = "symbols_prices_updated";

    /**
     * @var array
     */
    public $priceList;

    /**
     * Create a new event instance.
     *
     * @param Collection $prices
     */
    public function __construct($prices = [])
    {
        $this->priceList = fractal()->collection($prices)->transformWith(function ($symbol) {
            return [
                'symbol' => $symbol['symbol'],
                'price' => $symbol['price'],
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
