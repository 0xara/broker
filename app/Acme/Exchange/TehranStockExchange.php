<?php


namespace App\Acme\Exchange;


use App\Imports\TehranExchangeDailyCandlePriceImport;
use App\Models\TehranStockExchangeShare;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

//https://github.com/ghodsizadeh/tehran-stocks/blob/master/src/tehran_stocks/download/price.py

// stock history: http://members.tsetmc.com/tsev2/data/InstTradeHistory.aspx?i=${asset_id}&Top=999999&A=0

class TehranStockExchange implements Exchangable
{

    public static $analyzeStockPageDataResponse;
    public static $analyzeMarketWatchPageDataResponse;

    public static function getSymbols()
    {
        return TehranStockExchangeShare::select([
            'symbol',
            'group_name as quoteAsset'
        ])->get();
    }

    /**
     * another option is using this url:
     * http://www.tsetmc.com/Loader.aspx?ParTree=111C1417
     *
     * @return mixed
     */
    public static function getStockIds()
    {
        $response = Http::get("http://tsetmc.com/tsev2/data/MarketWatchPlus.aspx");

        preg_match_all('/\d{15,20}/s',$response->body(),$array);

        return array_unique($array[0]);
    }

    public static function getStockGroups()
    {
        $response = Http::get("http://www.tsetmc.com/Loader.aspx?ParTree=111C1213");

        preg_match_all('/\d{2}/s',$response->body(),$array);

        return $array[0];
    }

    public static function getStockDetail($stockCode)
    {
        $response = Http::get("http://www.tsetmc.com/Loader.aspx?ParTree=151311&i={$stockCode}");

        $name = with($response, function ($r) {
            preg_match("/LVal18AFC='(.*?)',/s",$r->body(),$array);
            $response = $array[1] ?? null;
            return $response ?: null;
        });

        if($name == "',DEven='',LSecVal='',CgrValCot='',Flow='',InstrumentID='")
            return false;

        return [
            "stock_code" => $stockCode,
            "instId" => with($response, function ($r) {
                preg_match("/InstrumentID='([\w\d]*)|$/s",$r->body(),$array);
                return $array[1];
            }),
            'insCode' => with($response, function ($r) use($stockCode) {
                preg_match("/InsCode='(\d*)',/s",$r->body(),$array);
                return $array[1] == $stockCode ? $stockCode : 0;
            }),
            "symbol" => $name,
            "group_name" => with($response, function ($r) {
                preg_match("/LSecVal='([\D]*)',/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
            "title" => with($response, function ($r) {
                preg_match("/Title='(.*?)',/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
            "sectorPe" => with($response, function ($r) {
                preg_match("/SectorPE='([\.\d]*)',/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
            "shareCount" => with($response, function ($r) {
                preg_match("/ZTitad=([\.\d]*),/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
            "estimatedEps" => with($response, function ($r) {
                preg_match("/EstimatedEPS='([\.\d]*)',/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
            "group_code" => with($response, function ($r) {
                preg_match("/CSecVal='([\w\d]*)|$',/s",$r->body(),$array);
                $response = $array[1] ?? null;
                return $response ?: null;
            }),
        ];
    }

    /**
     * @param $symbol
     * @param bool $update
     * @return mixed
     */
    public static function getSymbolPrice($symbol, $update = false)
    {
        if($update) self::$analyzeStockPageDataResponse = null;

        $stock = TehranStockExchangeShare::select(['stock_code'])->where('symbol','=',$symbol)->first();

        return self::analyzeStockPageData($stock->stock_code)['stockData']['price'];
    }

    /**
     * @param $stockCode
     * @param bool $update
     * @return |null
     */
    public function getIndexTickerPrice($stockCode, $update = false)
    {
        if($update) self::$analyzeStockPageDataResponse = null;

        $indexData = self::analyzeStockPageData($stockCode)['indexData'];

        return $indexData ? $indexData['index_value'] : null;
    }

    /**
     * @param $stockCode
     * @param $from
     * @param $to
     * @return array
     */
    public static function getStockCandles($stockCode, $from, $to)
    {
        return self::getStockCandlesData($stockCode, $from, $to)->getCandles();
    }

    /**
     * @param $stockCode
     * @return mixed|null
     */
    public static function getDelayedStockPrice($stockCode)
    {
        $now = Carbon::now()->format('Ymd');
        $lastWeek = Carbon::now()->subDays(7)->format('Ymd');

        return self::getStockCandlesData($stockCode, $lastWeek, $now)->getLastPrice();
    }

    /**
     * @param $stockCode
     * @param $from|"20000101"
     * @param $to
     * @return TehranExchangeDailyCandlePriceImport
     */
    public static function getStockCandlesData($stockCode, $from, $to)
    {
        $response = Http::get("http://www.tsetmc.com/tse/data/Export-txt.aspx?a=InsTrade&InsCode={$stockCode}&DateFrom={$from}&DateTo={$to}&b=0");

        if(!Storage::exists($path = 'excel-temp'.time()))
        {
            $result = Storage::makeDirectory($path, 0755, true);
        }

        Storage::put($filePath = "$path/temp.csv",$response->toPsrResponse()->getBody());

        Excel::import($candlesImport = (new TehranExchangeDailyCandlePriceImport()),$filePath);

        Storage::deleteDirectory($path);

        return $candlesImport;
    }

    /**
     * @param $stockCode
     * @return array
     */
    public static function analyzeStockPageData($stockCode)
    {
        $response =
            self::$analyzeStockPageDataResponse ?:
                self::$analyzeStockPageDataResponse = Http::get("http://www.tsetmc.com/tsev2/data/instinfofast.aspx?i={$stockCode}&c=34");

        $data = explode(';',preg_replace("/\r\n|\r|\n/",'',$response->body()));
        $stockData = explode(',',$data[0]);
        $indexData = $data[1] ? explode(',',$data[1]) : '';

        $stockResult = [
            'time' => trim($stockData[0]),
            'am-pm' => trim($stockData[1]),
            'price' => trim($stockData[2]), //'آخرین معامله'
            'end_price' => trim($stockData[3]), //پایانی
            'first_price' => trim($stockData[4]), //اولین
            'yesterday_price' => trim($stockData[5]), //دیروز
            'transactions_count' => trim($stockData[8]), //تعداد معاملات
            'transactions_volume' => trim($stockData[9]), //حجم معاملات
            'transactions_value' => trim($stockData[10]), //ارزش معاملات
            'date' => trim($stockData[12]), //تاریخ
            'last_update_time' => trim($stockData[13]), //آخرین اطلاعات قیمت
        ];

        $change = $indexData[3] ?? '';

        preg_match("/<div class='(\w{2})'>[(]?([^)]+)[)]?<\/div>(.+)%/s",$change,$changeResult);

        $indexResult = [
            'time' =>  with(trim($indexData[0] ?? ''), function ($dateTime) { //زمان
                if(!$dateTime) return '';
                return explode(' ',$dateTime)[1];
            }),
            'index_value' =>  $index_value = trim($indexData[2] ?? ''), //شاخص
            'change_from_yesterday' =>  trim($changeResult[2] ?? ''), //تغییر نسبت به دیروز
            'change_percentage' =>  trim($changeResult[3] ?? ''), //درصد تغییر
            'change_state_from_yesterday' =>  with(trim($changeResult[1] ?? ''), function ($state) {
                if(!in_array($state, ['pn', 'mn'])) return '';
                return $state == "pn" ? 'UP' : 'DOWN';
            }),
        ];

        return [
            'stockData' => $stockResult,
            'indexData' => $index_value ? $indexResult : null
        ];
    }

    /**
     *
     */
    public static function getSymbolsPrices()
    {
        $response = self::$analyzeMarketWatchPageDataResponse ?:
            self::$analyzeMarketWatchPageDataResponse = Http::get("http://www.tsetmc.com/tsev2/data/MarketWatchInit.aspx?h=0&r=0");

        $data = explode('@',$response->body());

        $indexData = explode(',',$data[1]);

        preg_match("/<div class='(\w{2})'>[(]?([^)]+)[)]?<\/div>(.+)%/s",$indexData[3],$indexResult);

        return collect(explode(';',$data[2]))->map(function ($item) {
            $data =  explode(',',$item);
            return [
                'stock_code' => $data[0],
                'instrument_id' => $data[1],
                'symbol' => $data[2],
                'symbol_name' => $data[3],
                'first_price' => $data[5],
                'end_price' => $data[6],
                'price' => $data[7],
                'transactions_count' => $data[8],
                'transactions_volume' => $data[9],
                'transactions_value' => $data[10],
                'min_price' => $data[11],
                'max_price' => $data[12],
                'yesterday_price' => $data[13],
                'estimated_eps' => $data[14],
                'group_code' => $data[18],
                'day_max_price' => $data[19],
                'day_min_price' => $data[20],
                'share_count' => $data[21],
            ];
        })->keyBy('stock_code')->prepend([
            'symbol' => 'شاخص کل',
            'value' => $indexData[2],
            'change_from_yesterday' => $indexResult[2],
            'change_percentage' => $indexResult[3],
            'change_state_from_yesterday' => in_array($indexResult[1],['pn','mn']) ? ($indexResult[1] == "pn" ? 'UP' : 'DOWN') : '',
        ],'index');
    }
}
