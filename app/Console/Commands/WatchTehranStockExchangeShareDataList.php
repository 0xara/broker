<?php

namespace App\Console\Commands;

use App\Acme\Exchange\TehranStockExchange;
use App\Models\TehranStockExchangeShare;
use Illuminate\Console\Command;

class WatchTehranStockExchangeShareDataList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:watch-tehran-stock-exchange-datalist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $stockIds = TehranStockExchange::getStockIds();

        $existIds = TehranStockExchangeShare::whereIn('stock_code',$stockIds)->pluck('stock_code')->all();

        $stockIds = array_diff($stockIds, $existIds);

        $insertData = [];

        foreach ($stockIds as $stockId)
        {
            if(!$detail = TehranStockExchange::getStockDetail($stockId)) continue;

            $insertData[] = $detail;
        }

        TehranStockExchangeShare::insert($insertData);
    }
}
