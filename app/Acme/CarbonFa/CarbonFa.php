<?php
/**
 * Created by PhpStorm.
 * User: arasharg
 * Date: 1/22/2016
 * Time: 5:39 PM.
 */

namespace App\Acme\CarbonFa;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class CarbonFa extends Jalalian
{
    use FaDateBase;

    protected $fail = false;

    public function format(string $format, $persianNumbers = false): string
    {
        return $persianNumbers ? self::convertToPersianNumbers(parent::format($format)): parent::format($format);
    }

    /**
     * @param $dateTime
     * @param bool $isJalali
     *
     * @return $this|Jalalian
     */
    public static function setCarbon($dateTime, $isJalali = false)
    {
        if($isJalali)
            return self::setJalaliCarbon($dateTime);

        $carbon = null;

        if($dateTime instanceof Carbon)
        {
            $carbon = self::fromCarbon(Carbon::parse($dateTime));
        }
        elseif(is_string($dateTime))
        {
            try{
                $carbon = self::fromCarbon(Carbon::parse($dateTime));
            }
            catch(\Exception $e){
                $carbon = self::fromCarbon(Carbon::now());

                $carbon->fail = true;
            }
        }
        else{
            $carbon = self::fromCarbon(Carbon::now());

            $carbon->fail = true;
        }

        return $carbon;
    }

    /**
     * @param $dateTime
     *
     * @return CarbonFa|Jalalian
     */
    public static function setJalaliCarbon($dateTime)
    {
        try{
            if(!is_string($dateTime))
            {
                $carbon = self::fromCarbon(Carbon::now());

                $carbon->fail = true;
            }

            $dateTime = self::convertToEnglishNumbers($dateTime);

            $dateTime = str_replace('  ', ' ', $dateTime);

            list($date, $time) = self::getValidatedJalaliDateTime($dateTime);

            $carbon = self::fromFormat('Y-m-d H:i:s', "{$date} {$time}");
        }
        catch(\Exception $e)
        {
            $carbon = self::fromCarbon(Carbon::now());

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

        return $this->format('Y/m/d', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJTime($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('H:i:s', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJYear($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('Y', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJMonth($persianNumbers = false, $name = false)
    {
        if($this->fail) return '';

        if($name)
            return $this->format('%B');

        return $this->format('m', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJDay($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('d', $persianNumbers);
    }

    /**
     * @return string
     */
    public function getJDayOfWeek()
    {
        return $this->format('%A');
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJHour($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('H', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJMinute($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('i', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function getJSecond($persianNumbers = false)
    {
        if($this->fail) return '';

        return $this->format('s', $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function toJalali($persianNumbers = false, $jFormat = 'Y/m/d H:i:s')
    {
        if($this->fail) return null;

        return $this->format($jFormat, $persianNumbers);
    }

    /**
     * @param bool $persianNumbers
     *
     * @return string
     */
    public function toGregorian($persianNumbers = false)
    {
        if($this->fail) return null;

        return !$persianNumbers
            ? $this->toCarbon()->toDateTimeString()
            : self::convertToPersianNumbers($this->toCarbon()->toDateTimeString());
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
        return $this->toCarbon();
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
        if(!$time) return '00:00:00';

        $timeItems = explode(':', $time);

        if(3 != count($timeItems)) return $time = '00:00:00';

        foreach($timeItems as $key => $timeItem)
        {
            if(!is_numeric($timeItem))
            {
                return '00:00:00';
            }

            $timeItems[$key] = self::minTwoDigits((int) $timeItem);
        }

        return implode(':',$timeItems);
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

        foreach($dateItems as $key => $dateItem)
        {
            if(!is_numeric($dateItem))
            {
                throw new \Exception();
            }

            $dateItems[$key] = self::minTwoDigits((int) $dateItem);
        }

        return implode('-',$dateItems);
    }

    /**
     * @param $number
     *
     * @return string
     */
    public static function minTwoDigits($number)
    {
        return $number < 10 ? "0{$number}" : $number;
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInYears($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInYears($date, $absolute);
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInMonths($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInMonths($date, $absolute);
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInDays($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInDays($date, $absolute);
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInHours($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInHours($date, $absolute);
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInMinutes($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInMinutes($date, $absolute);
    }

    /**
     * @param Jalalian|Carbon $date
     * @return int
     */
    public function diffInSeconds($date, $absolute = true)
    {
        $date = $date instanceof Jalalian ? $date->toCarbon() : $date;

        return $this->toCarbon()->diffInSeconds($date, $absolute);
    }

    /**
     * @return CarbonFa
     */
    public function copy()
    {
        return clone $this;
    }
}
