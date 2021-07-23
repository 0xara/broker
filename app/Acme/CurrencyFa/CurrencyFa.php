<?php


namespace App\Acme\CurrencyFa;


class CurrencyFa
{

    private static $instance;

    private $value;

    private $original;

    /**
     * @param CurrencyFa $value
     */
    public static function of($value)
    {
        self::$instance = self::$instance ?: new static();
        self::$instance->value = $value;
        self::$instance->original = $value;
        return self::$instance;
    }

    public function toRial()
    {
        if(is_null($this->value)) return $this;

        $this->value = $this->value * 10;

        return $this;
    }

    public function toToman()
    {
        if(is_null($this->value)) return $this;

        $this->value = $this->value / 10;

        return $this;
    }

    public function toCurrency($decimals = 2, $removeTailZero = true, $decimalpoint = '.', $separator = ',')
    {
        if(is_null($this->value)) return $this;

        if($removeTailZero && $decimals > 0)
        {
            $decimalZeroTail = '.'.str_repeat('0', $decimals);

            $this->value = str_replace($decimalZeroTail, '', number_format($this->value, $decimals, $decimalpoint, $separator));
        }
        else{
            $this->value = number_format($this->value, $decimals, $decimalpoint, $separator);
        }

        return $this;
    }

    public function toPersian()
    {
        if(is_null($this->value)) return $this;

        $this->value = self::convertToPersianNumbers($this->value);

        return $this;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public static function convertToPersianNumbers($matches)
    {
        $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english_array, $farsi_array, ''.$matches);
    }

    public function __toString()
    {
        return $this->value;
    }

}
