<?php

namespace App\Console\Commands;

use App\Acme\CarbonFa\CarbonFa;
use App\Acme\Exchange\SendAlertNotification;
use App\Acme\Exchange\TehranStockExchange;
use App\Events\TehranStockExchangeSymbolsPricesUpdated;
use App\Models\Alert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            return TehranStockExchange::getSymbolsPrices();
        }

        if($prices = self::cacheExists()) return $prices;

        return self::cachePrices();
    }

    public static function cachePrices()
    {
        $folder = Str::snake(class_basename(TehranStockExchange::class));
        $filename = CarbonFa::now()->format("Y_m_d");

        if(Storage::exists($path = "$folder/cache/"))
        {
            Storage::deleteDirectory($path);
        }

        $result = Storage::makeDirectory($path, 0755, true);

        Storage::put($filePath = "$path/$filename",$prices = TehranStockExchange::getSymbolsPrices()->toJson());

        return $result;
    }

    /**
     * @return false|array
     */
    public static function cacheExists()
    {
        $folder = Str::snake(class_basename(TehranStockExchange::class));
        $filename = CarbonFa::now()->format("Y_m_d");

        if(!Storage::exists($path = "$folder/cache/$filename")) return false;

        return json_decode(Storage::get($path), true);
    }

    public static function marketIsOpen()
    {
        $today = CarbonFa::now();

        return !$today->isThursday() && !$today->isFriday() &&
            $today->getHour() > 8 && $today->getHour() < 13;
    }
}
