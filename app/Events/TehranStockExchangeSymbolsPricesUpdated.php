<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Fractalistic\ArraySerializer;

class TehranStockExchangeSymbolsPricesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array
     */
    public $priceList;

    /**
     * Create a new event instance.
     *
     * @param array $prices
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
        return new Channel('tehran_stock_exchange');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'symbols_prices_updated';
    }
}
