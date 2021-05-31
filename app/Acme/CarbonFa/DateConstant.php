<?php
/**
 * Created by PhpStorm.
 * User: Alireza
 * Date: 4/13/2019
 * Time: 12:02 AM.
 */

namespace App\Acme\CarbonFa;

use Carbon\Carbon;

class DateConstant
{
    /**
     * @return array
     */
    public static function getMonths()
    {
        return [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
    }

    /**
     * @param bool $persianNumber
     *
     * @return array
     */
    public static function getDaysOfMonths($persianNumber = true)
    {
        $days = [];

        for($i = 1; $i < 32; ++$i)
        {
            $days[$i] = !$persianNumber ? $i : CarbonFa::convertToPersianNumbers($i);
        }

        return $days;
    }

    /**
     * @param int  $from
     * @param null $to
     * @param bool $persianNumber
     *
     * @return array
     */
    public static function getYears($from = 1300, $to = null, $persianNumber = true)
    {
        $years = [];

        $to = $to ?: CarbonFa::setCarbon(Carbon::now())->getJYear();

        for($i = $from; $i <= $to; ++$i)
        {
            $years[$i] = !$persianNumber ? $i : CarbonFa::convertToPersianNumbers($i);
        }

        return $years;
    }
}
