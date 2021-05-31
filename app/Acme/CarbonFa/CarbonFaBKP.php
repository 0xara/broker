<?php
/**
 * Created by PhpStorm.
 * User: arasharg
 * Date: 1/22/2016
 * Time: 5:39 PM.
 */

namespace App\Acme\CarbonFa;

use Carbon\Carbon;
use Illuminate\Support\Carbon as LaravelCarbon;

class CarbonFaBKP extends Carbon
{
    use FaDateBase;

    protected $fail = false;

    /**
     * @param $dateTime
     * @param bool $isJalali
     *
     * @return $this
     */
    public static function setCarbon($dateTime, $isJalali = false)
    {
        if($isJalali)
            return self::setJalaliCarbon($dateTime);

        $carbon = null;

        if($dateTime instanceof Carbon)
        {
            $carbon = $dateTime;

            return self::parse($carbon);
        }
        elseif(is_string($dateTime))
        {
            try{
                $carbon = self::parse($dateTime );
            }
            catch(\Exception $e){
                $carbon = self::parse('0000-01-01 00:00:00' );

                $carbon->fail = true;
            }
        }
        else{
            $carbon = self::parse('0000-01-01 00:00:00' );

            $carbon->fail = true;
        }

        return $carbon;
    }

    /**
     * @param $dateTime
     *
     * @return CarbonFa
     */
    public static function setJalaliCarbon($dateTime) {
        $carbon = null;

        try{
            if(!is_string($dateTime))
            {
                $carbon = self::parse('0000-01-01 00:00:00');

                $carbon->fail = true;
            }

            $dateTime = self::convertToEnglishNumbers($dateTime);

            $dateTime = str_replace('  ', ' ', $dateTime);

            list($date, $time) = self::getValidatedJalaliDateTime($dateTime);

            list($jHour, $jMinute, $jSecond) = explode(':', $time);

            list($jYear, $jMonth, $jDay) = explode('/', str_replace('-','/',$date));

            list($gYear, $gMonth, $gDay) = self::JalaliToGregorian($jYear, $jMonth, $jDay);

            $carbon = self::create($gYear, $gMonth, $gDay, $jHour, $jMinute, $jSecond);
        }
        catch(\Exception $e)
        {
            $carbon = self::parse('0000-01-01 00:00:00');

            $carbon->fail = true;
        }

        return $carbon;
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJDate($persianNumbers = false)
    {
        if($this->fail) return '';

        $date = self::GregorianToJalali($this->year, $this->month, $this->day);

        $date = implode('/', $date);

        return !$persianNumbers ? $date : self::convertToPersianNumbers($date);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJTime($persianNumbers = false)
    {
        if($this->fail) return '';

        return !$persianNumbers
            ? $this->toTimeString()
            : self::convertToPersianNumbers($this->toTimeString());
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJYear($persianNumbers = false)
    {
        if($this->fail) return '';

        $date = self::GregorianToJalali($this->year, $this->month, $this->day);

        return !$persianNumbers ? $date[0] : self::convertToPersianNumbers($date[0]);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJMonth($persianNumbers = false, $name = false)
    {
        if($this->fail) return '';

        $date = self::GregorianToJalali($this->year, $this->month, $this->day);

        if($name)
            return $this->getMonthName($date[1]);

        return !$persianNumbers ? $date[1] : self::convertToPersianNumbers($date[1]);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJDay($persianNumbers = false)
    {
        if($this->fail) return '';

        $date = self::GregorianToJalali($this->year, $this->month, $this->day);

        return !$persianNumbers ? $date[2] : self::convertToPersianNumbers($date[2]);
    }

    /**
     * @return string
     */
    public function getJDayOfWeek()
    {
        return $this->getDayName($this->dayOfWeek);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJHour($persianNumbers = false)
    {
        if($this->fail) return '';

        return !$persianNumbers
            ? $this->hour
            : self::convertToPersianNumbers($this->hour);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJMinute($persianNumbers = false)
    {
        if($this->fail) return '';

        return !$persianNumbers
            ? $this->minute
            : self::convertToPersianNumbers($this->minute);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJSecond($persianNumbers = false)
    {
        if($this->fail) return '';

        return !$persianNumbers
            ? $this->second
            : self::convertToPersianNumbers($this->second);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function toJalali($persianNumbers = false, $jFormat = null)
    {
        if($this->fail)
        {
            if($persianNumbers)
            {
                return self::convertToPersianNumbers('0000/00/00 00:00:00');
            }

            return '0000/00/00 00:00:00';
        }

        list($year, $month, $day) = self::GregorianToJalali($this->year, $this->month, $this->day);

        $month = self::minTwoDigits($month);
        $day = self::minTwoDigits($day);

        if(is_string($jFormat))
        {
            $dateTime = str_replace('MN', $this->getMonthName($month), $jFormat);
            $dateTime = str_replace('DN', $this->getDayName($this->dayOfWeek), $dateTime);
            $dateTime = str_replace('M', $month, $dateTime);
            $dateTime = str_replace('D', $day, $dateTime);
            $dateTime = str_replace('Y', $year, $dateTime);
            $dateTime = str_replace('y', substr_replace($year, '', 0, 2), $dateTime); //1394=>94
            $dateTime = str_replace('g', $this->hour, $dateTime);
            $dateTime = str_replace('h', self::minTwoDigits($this->hour), $dateTime);
            $dateTime = str_replace('i', self::minTwoDigits($this->minute), $dateTime);
            $dateTime = str_replace('s', self::minTwoDigits($this->second), $dateTime);
        }
        else{
            $date = "{$year}/{$month}/{$day}";

            $dateTime = $date.' '.$this->toTimeString();
        }

        return !$persianNumbers ? $dateTime : self::convertToPersianNumbers($dateTime);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function toGregorian($persianNumbers = false)
    {
        if($this->fail)
        {
            if($persianNumbers)
            {
                return self::convertToPersianNumbers('0000-00-00 00:00:00');
            }

            return '0000-00-00 00:00:00';
        }

        return !$persianNumbers
            ? $this->toDateTimeString()
            : self::convertToPersianNumbers($this->toDateTimeString());
    }

    /**
     * @return mixed
     */
    public function failed()
    {
        return (bool) $this->fail;
    }

    /**
     * @return $this|bool
     */
    public function passed()
    {
        return !$this->failed() ? $this : false;
    }

    /**
     * @return \Carbon\Carbon;
     */
    public function getCarbon()
    {
        return $this;
    }

    /**
     * @param $dateTime
     *
     * @throws \Exception
     *
     * @return array
     */
    private static function getValidatedJalaliDateTime($dateTime)
    {
        $dateTimeItems = explode(' ', $dateTime);

        if(count($dateTimeItems) > 2 || count($dateTimeItems) < 1)
        {
            throw new \Exception();
        }
        elseif(1 == count($dateTimeItems))
        {
            $dateTimeItems[] = '00:00:00';
        }

        list($date, $time) = $dateTimeItems;

        $date = self::validateJalaliDate($date);

        $time = self::validateTime($time);

        return [
            $date,
            $time,
        ];
    }

    /**
     * @param $time
     *
     * @return string
     */
    private static function validateTime($time)
    {
        if(!$time) $time = '00:00:00';

        $timeItems = explode(':', $time);

        if(3 != count($timeItems))
        {
            $time = '00:00:00';
        }

        foreach($timeItems as $timeItem)
        {
            if(!is_numeric($timeItem))
            {
                $time = '00:00:00';

                break;
            }
        }

        return $time;
    }

    /**
     * @param $date
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private static function validateJalaliDate($date)
    {
        $dateItems = explode('/', str_replace('-','/',$date));

        if(3 != count($dateItems))
        {
            throw new \Exception();
        }

        foreach($dateItems as $dateItem)
        {
            if(!is_numeric($dateItem))
            {
                throw new \Exception();
            }
        }

        return $date;
    }

    /**
     * @param $number
     *
     * @return string
     */
    private static function minTwoDigits($number)
    {
        return $number < 10 ? "0{$number}" : $number;
    }
}
