<?php
/**
 * Created by PhpStorm.
 * User: arasharg
 * Date: 1/22/2016
 * Time: 5:56 PM.
 */

namespace App\Acme\CarbonFa;

trait FaDateBase
{
    /**
     * The day constants.
     */
    public static $SUNDAY = 0;
    public static $MONDAY = 1;
    public static $TUESDAY = 2;
    public static $WEDNESDAY = 3;
    public static $THURSDAY = 4;
    public static $FRIDAY = 5;
    public static $SATURDAY = 6;

    private static function div($a, $b)
    {
        return (int) ($a / $b);
    }

    /**
     * Gregorian to Jalali Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi.
     */
    public static function GregorianToJalali($g_y, $g_m, $g_d)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }

        if ($gm > 1 && ((0 == $gy % 4 && 0 != $gy % 100) || (0 == $gy % 400))) {
            ++$g_day_no;
        }

        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * self::div($j_day_no, 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
            $j_day_no -= $j_days_in_month[$i];
        }

        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return [$jy, $jm, $jd];
    }

    /**
     * Jalali to Gregorian Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi.
     */
    public static function JalaliToGregorian($j_y, $j_m, $j_d)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + self::div($jy, 33) * 8 + self::div($jy % 33 + 3, 4);
        for ($i = 0; $i < $jm; ++$i) {
            $j_day_no += $j_days_in_month[$i];
        }

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            --$g_day_no;
            $gy += 100 * self::div($g_day_no, 36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365) {
                ++$g_day_no;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            --$g_day_no;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + (1 == $i && $leap); ++$i) {
            $g_day_no -= $g_days_in_month[$i] + (1 == $i && $leap);
        }

        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return [$gy, $gm, $gd];
    }

    public static function convertToPersianNumbers($matches)
    {
        $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english_array, $farsi_array, $matches);
    }

    public static function convertToEnglishNumbers($matches)
    {
        $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($farsi_array, $english_array, $matches);
    }

    public function getMonthName($month, $shorten = false, $len = 3)
    {
        $ret = '';
        switch ($month) {
            case '1':
                $ret = 'فروردین';
                break;
            case '2':
                $ret = 'اردیبهشت';
                break;
            case '3':
                $ret = 'خرداد';
                break;
            case '4':
                $ret = 'تیر';
                break;
            case '5':
                $ret = 'مرداد';
                break;
            case '6':
                $ret = 'شهریور';
                break;
            case '7':
                $ret = 'مهر';
                break;
            case '8':
                $ret = 'آبان';
                break;
            case '9':
                $ret = 'آذر';
                break;
            case '10':
                $ret = 'دی';
                break;
            case '11':
                $ret = 'بهمن';
                break;
            case '12':
                $ret = 'اسفند';
                break;
        }

        return ($shorten) ? mb_substr($ret, 0, $len, 'UTF-8') : $ret;
    }

    public function getDayName($day, $shorten = false, $len = 1, $numeric = false)
    {
        $ret = '';

        if($day > 6)
        {
            return $ret;
        }

        switch (strtolower($day)) {
            case self::$SATURDAY:
                $ret = 'شنبه';
                $n = 1;
                break;
            case self::$SUNDAY:
                $ret = 'یکشنبه';
                $n = 2;
                break;
            case self::$MONDAY:
                $ret = 'دوشنبه';
                $n = 3;
                break;
            case self::$TUESDAY:
                $ret = 'سه شنبه';
                $n = 4;
                break;
            case self::$WEDNESDAY:
                $ret = 'چهارشنبه';
                $n = 5;
                break;
            case self::$THURSDAY:
                $ret = 'پنجشنبه';
                $n = 6;
                break;
            case self::$FRIDAY:
                $ret = 'جمعه';
                $n = 7;
                break;
        }

        return ($numeric) ? $n : (($shorten) ? mb_substr($ret, 0, $len, 'UTF-8') : $ret);
    }
}
