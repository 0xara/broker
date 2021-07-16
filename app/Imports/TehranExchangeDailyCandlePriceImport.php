<?php
namespace App\Imports;


use App\Acme\CarbonFa\CarbonFa;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TehranExchangeDailyCandlePriceImport implements ToCollection, WithStartRow
{
    /**
     * @var array
     */
    private $candles = [];

    /**
     * SessionInvitationsImport constructor.
     * @param $offering
     */
    public function __construct()
    {
    }

    /**
     * fields: username|name|last_name|password|expired_at
     *
     * @inheritDoc
     * @throws \Illuminate\Validation\ValidationException
     */
    public function collection(Collection $rows)
    {
        \DB::transaction(function () use ($rows) {
           $this->handleCandles($rows);
        });
    }

    /**
     * @param Collection $rows
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function handleCandles(Collection $rows)
    {
        foreach ($rows as $key => $row)
        {
            $data = [
                'symbol' => $row[0] ?? null,
                'date' => $row[1] ?? null,
                'open' => $row[2] ?? null,
                'high' => $row[3] ?? null,
                'low' => $row[4] ?? null,
                'close' => $row[5] ?? null,
                'volume' => $row[8] ?? null,
                'timeFrame' => $row[10] ?? null,
            ];

            $this->candles[$key] = $data;
        }

        ksort($this->candles);
        $this->candles = array_values($this->candles);
    }

    /**
     * @return array
     */
    public function getCandles(): array
    {
        return $this->candles;
    }

    /**
     * @return mixed|null
     */
    public function getLastCandle()
    {
        if(!count($this->candles)) return null;

        return $this->candles[count($this->candles) - 1];
    }

    /**
     * @return |null
     */
    public function getLastPrice()
    {
        if(!$lastCandle = $this->getLastCandle()) return null;

        return $lastCandle['close'];
    }

    /**
     * @inheritDoc
     */
    public function startRow(): int
    {
        return 2;
    }
}
